<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    local-mail
 * @copyright  Albert Gasset <albert.gasset@gmail.com>
 * @copyright  Marc Català <reskit@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once('label.class.php');

define('LOCAL_MAIL_MESSAGE_VISIBLE', 0);
define('LOCAL_MAIL_MESSAGE_DELETED', 1);
define('LOCAL_MAIL_MESSAGE_INVISIBLE', 2);

class local_mail_message {

    private static $indextypes = array(
        'inbox', 'drafts', 'sent', 'starred', 'course', 'label', 'trash'
    );

    private $id;
    private $course;
    private $subject;
    private $content;
    private $format;
    private $attachments = 0;
    private $draft;
    private $time;
    private $refs = array();
    private $users = array();
    private $role = array();
    private $unread = array();
    private $starred = array();
    private $deleted = array();
    private $labels = array();

    static public function count_index($userid, $type, $itemid=0) {
        global $DB;

        assert(in_array($type, self::$indextypes));

        $conditions = array('userid' => $userid, 'type' => $type, 'item' => $itemid);
        return $DB->count_records('local_mail_index', $conditions);
    }

    static public function count_menu($userid) {
        global $DB;

        $result = new stdClass;
        $result->courses = array();
        $result->labels = array();

        $sql = 'SELECT MIN(id), type, item, unread, COUNT(*) AS count'
            . ' FROM {local_mail_index}'
            . ' WHERE userid = :userid'
            . ' GROUP BY type, item, unread'
            . ' ORDER BY type DESC';

        $records = $DB->get_records_sql($sql, array('userid' => $userid));

        foreach ($records as $record) {
            if ($record->type == 'inbox' and $record->unread) {
                $result->inbox = (int) $record->count;
            } else if ($record->type == 'drafts') {
                if (!isset($result->drafts)) {
                    $result->drafts = 0;
                }
                $result->drafts += (int) $record->count;
            } else if ($record->type == 'course' and $record->unread) {
                $context = context_course::instance($record->item);
                if (!has_capability('local/mail:usemail', $context)) {
                    $result->inbox -= (int) $record->count;
                }
                $result->courses[(int) $record->item] = (int) $record->count;
            } else if ($record->type == 'label' and $record->unread) {
                $result->labels[(int) $record->item] = (int) $record->count;
            }
        }

        return $result;
    }

    static public function create($userid, $courseid, $time=false) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        $message = new self;
        $message->course = self::fetch_course($courseid);
        $message->users[$userid] = self::fetch_user($userid);

        $record = new stdClass;
        $record->courseid = $message->course->id;
        $record->subject = $message->subject = '';
        $record->content = $message->content = '';
        $record->format = $message->format = -1;
        $record->draft = $message->draft = true;
        $record->time = $message->time = $time ?: time();
        $message->id = $DB->insert_record('local_mail_messages', $record);

        $record = new stdClass;
        $record->messageid = $message->id;
        $record->userid = $userid;
        $record->role = $message->role[$userid] = 'from';
        $record->unread = $message->unread[$userid] = false;
        $record->starred = $message->starred[$userid] = false;
        $record->deleted = $message->deleted[$userid] = LOCAL_MAIL_MESSAGE_VISIBLE;
        $DB->insert_record('local_mail_message_users', $record);

        $message->create_index($userid, 'drafts');
        $message->create_index($userid, 'course', $courseid);

        $transaction->allow_commit();

        return $message;
    }

    static public function delete_course($courseid) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();
        $select = 'messageid IN (SELECT id FROM {local_mail_messages} WHERE courseid = :courseid)';
        $params = array('courseid' => $courseid);
        $DB->delete_records_select('local_mail_index', $select, $params);
        $DB->delete_records_select('local_mail_message_labels', $select, $params);
        $DB->delete_records_select('local_mail_message_users', $select, $params);
        $DB->delete_records_select('local_mail_message_refs', $select, $params);
        $DB->delete_records('local_mail_messages', $params);
        $transaction->allow_commit();
    }

    static public function fetch($id) {
        $messages = self::fetch_many(array($id));
        return reset($messages);
    }

    static public function fetch_index($userid, $type, $item=0, $limitfrom=0, $limitnum=0) {
        global $DB;

        assert(in_array($type, self::$indextypes));

        $conditions = array('userid' => $userid, 'type' => $type, 'item' => $item);
        $records = $DB->get_records('local_mail_index', $conditions, 'time DESC, messageid DESC',
                                    'messageid', $limitfrom, $limitnum);
        return self::fetch_many(array_keys($records));
    }

    static public function fetch_many(array $ids) {
        global $DB;

        $messages = array();

        if (!$ids) {
            return $messages;
        }

        $sql = 'SELECT courseid'
            . ' FROM {local_mail_messages}'
            . ' WHERE id  IN (' . implode(',', $ids) . ')'
            . ' GROUP BY courseid';

        if ($courses = $DB->get_records_sql_menu($sql)) {
            foreach (array_keys($courses) as $courseid) {
                $context = context_course::instance($courseid);
                if (!has_capability('local/mail:usemail', $context)) {
                    unset($courses[$courseid]);
                }
            }
        }

        if (!$courses) {
            return $messages;
        }

        $sql = 'SELECT m.id, m.courseid, m.subject, m.content, m.format, m.attachments, '
            . ' m.draft, m.time, c.shortname, c.fullname, c.groupmode'
            . ' FROM {local_mail_messages} m'
            . ' JOIN {course} c ON c.id = m.courseid'
            . ' AND m.courseid IN (' . implode(',', array_keys($courses)) . ')'
            . ' WHERE m.id  IN (' . implode(',', $ids) . ')';
        $records = $DB->get_records_sql($sql);

        $sql = 'SELECT mr.id AS recordid, mr.messageid, mr.reference'
            . ' FROM {local_mail_message_refs} mr'
            . ' WHERE mr.messageid IN (' . implode(',', $ids) . ')'
            . ' ORDER BY mr.id ASC';
        $refrecords = $DB->get_records_sql($sql);

        $userfields = \core_user\fields::for_userpic();
        $userfields->including(...array('username', 'maildisplay'));
        $mainuserfields = $userfields->get_sql('u', false, '', 'usermoodleid', false)->selects;
        $sql = 'SELECT mu.id AS recordid, mu.messageid, mu.userid, mu.role,'
            . ' mu.unread, mu.starred, mu.deleted,'
            . $mainuserfields
            . ' FROM {local_mail_message_users} mu'
            . ' JOIN {user} u ON u.id = mu.userid'
            . ' WHERE mu.messageid  IN (' . implode(',', $ids) . ')'
            . ' ORDER BY u.lastname, u.firstname';
        $userrecords = $DB->get_records_sql($sql);

        $sql = 'SELECT ml.id AS recordid, ml.messageid, l.id, l.userid, l.name, l.color'
            . ' FROM {local_mail_message_labels} ml'
            . ' JOIN {local_mail_labels} l ON l.id = ml.labelid'
            . ' WHERE ml.messageid  IN (' . implode(',', $ids) . ')'
            . ' ORDER BY l.name';
        $labelrecords = $DB->get_records_sql($sql);

        foreach (array_intersect($ids, array_keys($records)) as $id) {
            $messages[] = self::from_records($records[$id], $refrecords,
                                                 $userrecords, $labelrecords);
        }

        return $messages;
    }

    static public function search_index($userid, $type, $item, array $query) {
        global $DB;

        assert(in_array($type, self::$indextypes));
        assert(empty($query['before']) or empty($query['after']));

        $query['pattern'] = isset($query['pattern']) ? trim($query['pattern']) : '';
        $query['searchfrom'] = isset($query['searchfrom']) ? trim($query['searchfrom']) : '';
        $query['searchto'] = isset($query['searchto']) ? trim($query['searchto']) : '';

        $sql = 'SELECT i.messageid FROM {local_mail_index} i';
        if ($query['pattern'] !== '' OR !empty($query['attach'])) {
            $sql .= ' JOIN {local_mail_messages} m ON m.id = i.messageid';
        }
        $sql .= ' WHERE i.userid = :userid AND type = :type AND i.item = :item';
        $params = array('userid' => $userid, 'type' => $type, 'item' => $item);
        $order = 'DESC';

        if ($query['pattern'] !== '') {
            list($usersql, $userparams) = users_search_sql($query['pattern']);
            list($rolesql, $roleparams) = $DB->get_in_or_equal(['from', 'to', 'cc'], SQL_PARAMS_NAMED, 'role');
            $subjectsql = $DB->sql_like('m.normalizedsubject', ':pattern', false, false);
            $contentsql = $DB->sql_like('m.normalizedcontent', ':pattern2', false, false);
            $messageusersql =  'SELECT mu.messageid FROM {local_mail_message_users} mu'
                . " JOIN {user} u ON u.id = mu.userid WHERE mu.role $rolesql AND $usersql";
            $sql .= " AND (($subjectsql) OR ($contentsql) OR i.messageid IN ($messageusersql))";
            $params['pattern'] = '%' . $DB->sql_like_escape(self::normalize_text($query['pattern'])) . '%';
            $params['pattern2'] = $params['pattern'];
            $params = array_merge($params, $userparams, $roleparams);
        }

        if ($query['searchfrom'] !== '') {
            list($usersql, $userparams) = users_search_sql($query['searchfrom']);
            $messageusersql = 'SELECT mu.messageid FROM {local_mail_message_users} mu'
                . " JOIN {user} u ON u.id = mu.userid WHERE mu.role = :rolefrom AND $usersql";
            $sql .=  " AND i.messageid IN ($messageusersql)";
            $params['rolefrom'] = 'from';
            $params = array_merge($params, $userparams);
        }

        if ($query['searchto'] !== '') {
            list($usersql, $userparams) = users_search_sql($query['searchto']);
            $messageusersql = 'SELECT mu.messageid FROM {local_mail_message_users} mu'
                . " JOIN {user} u ON u.id = mu.userid WHERE (mu.role = :roleto OR mu.role = :rolecc) AND $usersql";
            $sql .=  " AND i.messageid IN ($messageusersql)";
            $params['roleto'] = 'to';
            $params['rolecc'] = 'cc';
            $params = array_merge($params, $userparams);
        }

        if (!empty($query['attach'])) {
            $sql .= ' AND m.attachments > 0';
        }

        if (!empty($query['time'])) {
            $sql .= ' AND i.time <= :time';
            $params['time'] = $query['time'];
        }

        if (!empty($query['unread'])) {
            $sql .= ' AND i.unread = 1';
        }

        if (!empty($query['before'])) {
            $from = self::fetch($query['before']);
            $sql .= ' AND i.time <= :beforetime AND (i.time < :beforetime2 OR i.messageid < :beforeid)';
            $params['beforetime'] = $from->time();
            $params['beforetime2'] = $from->time();
            $params['beforeid'] = $from->id();
        } else if (!empty($query['after'])) {
            $from = self::fetch($query['after']);
            $sql .= ' AND i.time >= :aftertime AND (i.time > :aftertime2 OR i.messageid > :afterid)';
            $params['aftertime'] = $from->time();
            $params['aftertime2'] = $from->time();
            $params['afterid'] = $from->id();
            $order = 'ASC';
        }

        $sql .= " ORDER BY i.time $order, i.messageid $order";
        $limitnum = !empty($query['limit']) ? $query['limit'] : 0;
        $records = $DB->get_records_sql($sql, $params, 0, $limitnum);
        $messages = self::fetch_many(array_keys($records));
        return !empty($query['after']) ? array_reverse($messages) : $messages;
    }

    static public function empty_trash($userid) {
        global $DB;

        $messages = self::fetch_index($userid, 'trash');

        if (empty($messages)) {
            return;
        }

        foreach ($messages as $message) {
            $message->set_invisible($userid);
        }
    }

    public function add_label(local_mail_label $label) {
        global $DB;

        assert($this->has_user($label->userid()));
        assert(!$this->draft or $this->role[$label->userid()] == 'from');
        assert(!$this->deleted($label->userid()));

        if (!isset($this->labels[$label->id()])) {
            $transaction = $DB->start_delegated_transaction();
            $record = new stdClass;
            $record->messageid = $this->id;
            $record->labelid = $label->id();
            $DB->insert_record('local_mail_message_labels', $record);
            $this->create_index($label->userid(), 'label', $label->id());
            $transaction->allow_commit();
            $this->labels[$label->id()] = $label;
        }
    }

    public function add_recipient($role, $userid) {
        global $DB;

        assert($this->draft);
        assert(!$this->has_recipient($userid));
        assert(in_array($role, array('to', 'cc', 'bcc')));

        $this->users[$userid] = self::fetch_user($userid);

        $record = new stdClass;
        $record->messageid = $this->id;
        $record->userid = $userid;
        $record->role = $this->role[$userid] = $role;
        $record->unread = $this->unread[$userid] = true;
        $record->starred = $this->starred[$userid] = false;
        $record->deleted = $this->deleted[$userid] = LOCAL_MAIL_MESSAGE_VISIBLE;
        $DB->insert_record('local_mail_message_users', $record);
    }

    public function attachments($includerefs=false) {
        global $DB;

        $attachments = $this->attachments;

        if ($includerefs and !empty($this->refs)) {
            list($sqlid, $params) = $DB->get_in_or_equal($this->refs, SQL_PARAMS_NAMED, 'messageid');
            $attachments += $DB->get_field_select('local_mail_messages', 'SUM(attachments)', "id $sqlid", $params);
        }

        return $attachments;
    }

    public function content() {
        return $this->content;
    }

    public function course() {
        return $this->course;
    }

    public function deleted($userid) {
        assert($this->has_user($userid));
        return $this->deleted[$userid];
    }

    public function discard() {
        global $DB;

        assert($this->draft);

        $transaction = $DB->start_delegated_transaction();
        $DB->delete_records('local_mail_messages', array('id' => $this->id));
        $DB->delete_records('local_mail_message_refs', array('messageid' => $this->id));
        $DB->delete_records('local_mail_message_users', array('messageid' => $this->id));
        $DB->delete_records('local_mail_message_labels', array('messageid' => $this->id));
        $DB->delete_records('local_mail_index', array('messageid' => $this->id));
        $transaction->allow_commit();
    }


    public function draft() {
        return $this->draft;
    }

    public function editable($userid) {
        return $this->draft and $this->has_user($userid) and $this->role[$userid] == 'from';
    }

    public function format() {
        return $this->format;
    }

    public function forward($userid, $time=false) {
        global $DB;

        assert(!$this->draft);
        assert($this->has_user($userid));

        $transaction = $DB->start_delegated_transaction();

        $message = self::create($userid, $this->course->id, $time);
        $message->save('FW: ' . $this->subject, '', -1, 0, $time);
        $message->set_references($this);

        foreach ($this->labels($userid) as $label) {
            $message->add_label($label);
        }

        $transaction->allow_commit();

        return $message;
    }

    public function replysend($userid, $all=false, $time=false) {
        global $DB;

        assert(!$this->draft);
        assert($this->has_user($userid) and $this->sender()->id == $userid);

        $transaction = $DB->start_delegated_transaction();

        $message = self::create($userid, $this->course->id, $time);
        $message->save($this->subject, '', -1, 0, $time);
        $message->set_references($this);

        foreach ($this->recipients('to') as $user) {
            if ($user->id != $userid) {
                $message->add_recipient('to', $user->id);
            }
        }

        if ($all) {
            foreach ($this->recipients('cc') as $user) {
                if ($user->id != $userid) {
                    $message->add_recipient('cc', $user->id);
                }
            }
        }

        foreach ($this->labels($userid) as $label) {
            $message->add_label($label);
        }

        $transaction->allow_commit();

        return $message;
    }

    public function has_label(local_mail_label $label) {
        return isset($this->labels[$label->id()]);
    }

    public function has_recipient($userid) {
        return $this->has_user($userid) and $this->role[$userid] != 'from';
    }

    public function id() {
        return $this->id;
    }

    public function labels($userid=false) {
        assert($userid === false or $this->has_user($userid));

        $result = array();
        foreach ($this->labels as $label) {
            if (!$userid or $label->userid() == $userid) {
                $result[] = $label;
            }
        }
        return $result;
    }

    public function recipients() {
        $roles = func_get_args();
        $result = array();
        foreach ($this->users as $user) {
            $role = $this->role[$user->id];
            if ($role != 'from' and (!$roles or in_array($role, $roles))) {
                $result[] = $user;
            }
        }
        return $result;
    }

    public function references() {
        $result = self::fetch_many($this->refs);
        usort($result, function($a, $b) {
            return $b->time() - $a->time();
        });
        return $result;
    }

    public function remove_label(local_mail_label $label) {
        global $DB;
        assert($this->has_user($label->userid()));
        assert(!$this->draft or $this->role[$label->userid()] == 'from');
        assert($this->deleted($label->userid()) == LOCAL_MAIL_MESSAGE_VISIBLE
            or $this->deleted($label->userid()) == LOCAL_MAIL_MESSAGE_INVISIBLE);

        if (isset($this->labels[$label->id()])) {
            $transaction = $DB->start_delegated_transaction();
            $conditions = array('messageid' => $this->id, 'labelid' => $label->id());
            $DB->delete_records('local_mail_message_labels', $conditions);
            $this->delete_index($label->userid(), 'label', $label->id());
            $transaction->allow_commit();
            unset($this->labels[$label->id()]);
        }
    }

    public function remove_recipient($userid) {
        global $DB;

        assert($this->draft);
        assert($this->has_recipient($userid));

        $DB->delete_records('local_mail_message_users', array(
            'messageid' => $this->id,
            'userid' => $userid,
        ));

        unset($this->users[$userid]);
        unset($this->role[$userid]);
        unset($this->unread[$userid]);
        unset($this->starred[$userid]);
        unset($this->deleted[$userid]);
    }

    public function reply($userid, $all=false, $time=false) {
        global $DB;

        assert(!$this->draft and $this->has_recipient($userid));
        assert(!$all or in_array($this->role[$userid], array('to', 'cc')));

        if (preg_match('/^RE\s*(?:\[(\d+)\])?:\s*(.*)$/', $this->subject, $matches)) {
            $nreply = $matches[1] ? (int) $matches[1] + 1 : 2;
            $subject = "RE [$nreply]: {$matches[2]}";
        } else {
            $subject = 'RE: ' . $this->subject;
        }

        $transaction = $DB->start_delegated_transaction();

        $message = self::create($userid, $this->course->id, $time);
        $message->save($subject, '', -1, 0, $time);
        $sender = $this->sender();
        $message->add_recipient('to', $sender->id);
        $message->set_references($this);

        if ($all) {
            foreach ($this->recipients('to', 'cc') as $user) {
                if ($user->id != $userid) {
                    $message->add_recipient('cc', $user->id);
                }
            }
        }

        foreach ($this->labels($userid) as $label) {
            $message->add_label($label);
        }

        $transaction->allow_commit();

        return $message;
    }

    public function save($subject, $content, $format, $attachments=0, $time=false) {
        global $DB;

        assert($this->draft);

        if (strlen($subject) > 100) {
            $subject = core_text::substr($subject, 0, 97) . '...';
        }

        $record = new stdClass;
        $record->id = $this->id;
        $record->subject = $this->subject = $subject;
        $record->content = $this->content = $content;
        $record->format = $this->format = $format;
        $record->attachments = $this->attachments = $attachments;
        $record->time = $this->time = $time ?: time();
        $record->normalizedsubject = self::normalize_text($record->subject);

        $context = context_course::instance($this->course->id);
        $content = file_rewrite_pluginfile_urls($content, 'pluginfile.php', $context->id,
                                                'local_mail', 'message', $this->id);
        $content = format_text($this->content, $format, ['filter' => false, 'nocache' => true]);
        $record->normalizedcontent = self::normalize_text(html_to_text($content, 0, false));

        $transaction = $DB->start_delegated_transaction();
        $DB->update_record('local_mail_messages', $record);
        $DB->set_field('local_mail_index', 'time', $this->time, array(
            'messageid' => $this->id,
        ));

        $transaction->allow_commit();
    }

    public function send($time=false) {
        global $DB;

        assert($this->draft and count($this->recipients()) > 0);

        $transaction = $DB->start_delegated_transaction();

        $record = new stdClass;
        $record->id = $this->id;
        $record->draft = $this->draft = false;
        $record->time = $this->time = $time ?: time();
        $DB->update_record('local_mail_messages', $record);

        $DB->set_field('local_mail_index', 'time', $this->time, array(
            'messageid' => $this->id,
        ));

        $DB->set_field('local_mail_index', 'type', 'sent', array(
            'messageid' => $this->id,
            'userid' => $this->sender()->id,
            'type' => 'drafts',
        ));

        foreach ($this->recipients() as $user) {
            $this->create_index($user->id, 'inbox');
            $this->create_index($user->id, 'course', $this->course->id);
        }

        foreach ($this->references() as $reference) {
            foreach ($this->recipients() as $user) {
                if ($reference->has_user($user->id)) {
                    foreach ($reference->labels($user->id) as $label) {
                        $this->add_label($label);
                    }
                }
            }
        }

        $transaction->allow_commit();
    }

    public function sender() {
        $userid = array_search('from', $this->role);
        return $this->users[$userid];
    }

    public function set_deleted($userid, $value) {
        global $DB;

        assert($this->has_user($userid));
        assert(!$this->draft or $this->role[$userid] == 'from');

        if ($this->deleted[$userid] == $value) {
            return;
        }

        $transaction = $DB->start_delegated_transaction();

        $conditions = array('messageid' => $this->id, 'userid' => $userid);
        $DB->set_field('local_mail_message_users', 'deleted', $value, $conditions);

        if ($value) {
            $this->delete_index($userid);
            $this->create_index($userid, 'trash');
        } else {
            $this->delete_index($userid, 'trash');
            if ($this->role[$userid] == 'from') {
                $this->create_index($userid, $this->draft ? 'drafts' : 'sent');
            } else {
                $this->create_index($userid, 'inbox');
            }
            if ($this->starred($userid)) {
                $this->create_index($userid, 'starred');
            }
            $this->create_index($userid, 'course', $this->course->id);
            foreach ($this->labels($userid) as $label) {
                $this->create_index($userid, 'label', $label->id());
            }
        }

        $transaction->allow_commit();

        $this->deleted[$userid] = $value;
    }

    public function set_invisible($userid) {
        global $DB;

        assert($this->has_user($userid));
        assert(!$this->draft or $this->role[$userid] == 'from');
        assert($this->deleted[$userid]);

        if ($this->deleted[$userid] == LOCAL_MAIL_MESSAGE_INVISIBLE) {
            return;
        }

        $transaction = $DB->start_delegated_transaction();
        $conditions = array('messageid' => $this->id, 'userid' => $userid);
        $DB->set_field('local_mail_message_users', 'deleted', LOCAL_MAIL_MESSAGE_INVISIBLE, $conditions);
        $this->delete_index($userid);
        $transaction->allow_commit();

        $this->deleted[$userid] = LOCAL_MAIL_MESSAGE_INVISIBLE;
        foreach ($this->labels($userid) as $label) {
            $this->remove_label($label);
        }
    }

    public function set_starred($userid, $value) {
        global $DB;

        assert($this->has_user($userid));
        assert(!$this->draft or $this->role[$userid] == 'from');
        assert(!$this->deleted($userid));

        if ($this->starred[$userid] == (bool) $value) {
            return;
        }

        $transaction = $DB->start_delegated_transaction();

        $conditions = array('messageid' => $this->id, 'userid' => $userid);
        $DB->set_field('local_mail_message_users', 'starred', (bool) $value, $conditions);

        if ($value) {
            $this->create_index($userid, 'starred');
        } else {
            $this->delete_index($userid, 'starred');
        }

        $transaction->allow_commit();

        $this->starred[$userid] = (bool) $value;
    }

    public function set_unread($userid, $value) {
        global $DB;

        assert($this->has_user($userid));
        assert(!$this->draft or $this->role[$userid] == 'from');

        if ($this->unread[$userid] == (bool) $value) {
            return;
        }

        $transaction = $DB->start_delegated_transaction();
        $conditions = array('messageid' => $this->id, 'userid' => $userid);
        $DB->set_field('local_mail_message_users', 'unread', (bool) $value, $conditions);
        $DB->set_field('local_mail_index', 'unread', (bool) $value, $conditions);
        $transaction->allow_commit();

        $this->unread[$userid] = (bool) $value;
    }

    public function starred($userid) {
        assert($this->has_user($userid));
        return $this->starred[$userid];
    }

    public function subject() {
        return $this->subject;
    }

    public function time() {
        return $this->time;
    }

    public function unread($userid) {
        assert($this->has_user($userid));
        return $this->unread[$userid];
    }

    public function viewable($userid, $includerefs=false) {
        global $DB;

        if ($this->has_user($userid)) {
            return ($this->deleted[$userid] != LOCAL_MAIL_MESSAGE_INVISIBLE
                    and (!$this->draft or $this->role[$userid] == 'from'));
        }

        if ($includerefs) {
            $sql = 'SELECT m.id'
                . ' FROM {local_mail_messages} m'
                . ' JOIN {local_mail_message_users} mu ON mu.messageid = m.id'
                . ' JOIN {local_mail_message_refs} mr ON mr.messageid = m.id'
                . ' WHERE mr.reference = :messageid'
                . ' AND mu.userid = :userid'
                . ' AND (m.draft = 0 OR mu.role = :role)';
            $params = array(
                'role' => 'from',
                'messageid' => $this->id,
                'userid' => $userid,
            );
            return $DB->record_exists_sql($sql, $params);
        }

        return false;
    }

    private function __construct() {
    }

    private static function from_records($record, $refrecords, $userrecords, $labelrecords) {
        $message = new self;
        $message->id = (int) $record->id;
        $message->course = (object) array(
            'id' => $record->courseid,
            'shortname' => $record->shortname,
            'fullname' => $record->fullname,
            'groupmode' => $record->groupmode,
        );
        $message->subject = $record->subject;
        $message->content = $record->content;
        $message->format = (int) $record->format;
        $message->attachments = (int) $record->attachments;
        $message->draft = (bool) $record->draft;
        $message->time = (int) $record->time;

        foreach ($refrecords as $r) {
            if ($r->messageid == $record->id) {
                $message->refs[] = $r->reference;
            }
        }

        foreach ($userrecords as $r) {
            if ($r->messageid == $record->id) {
                $message->role[$r->userid] = $r->role;
                $message->unread[$r->userid] = (bool) $r->unread;
                $message->starred[$r->userid] = (bool) $r->starred;
                $message->deleted[$r->userid] = (int) $r->deleted;
                $userfields = \core_user\fields::for_userpic();
                $userfields->including(...array('username', 'maildisplay'));
                $fields = $userfields->get_sql('', false, '', '', false)->selects;
                $userfields = array();
                foreach (explode(', ', $fields) as $value) {
                    if ($value === 'id') {
                        continue;
                    }
                    $userfields[$value] = isset($r->$value) ? $r->$value : '';
                }
                $userfields['id'] = $r->userid;
                $message->users[$r->userid] = (object) $userfields;
            }
        }

        foreach ($labelrecords as $r) {
            if ($r->messageid == $record->id) {
                $message->labels[$r->id] = local_mail_label::from_record($r);
            }
        }

        return $message;
    }

    private static function fetch_course($courseid) {
        global $DB;
        $conditions = array('id' => $courseid);
        $fields = 'id, shortname, fullname, groupmode';
        return $DB->get_record('course', $conditions, $fields, MUST_EXIST);
    }

    private static function fetch_user($userid) {
        global $DB;
        $conditions = array('id' => $userid);
        $userfields = \core_user\fields::for_userpic();
        $userfields->including(...array('username', 'maildisplay'));
        $fields = $userfields->get_sql('', false, '', '', false)->selects;
        return $DB->get_record('user', $conditions, $fields, MUST_EXIST);
    }

    private function create_index($userid, $type, $itemid=0) {
        global $DB;

        $record = new stdClass;
        $record->userid = $userid;
        $record->type = $type;
        $record->item = $itemid;
        $record->time = $this->time;
        $record->messageid = $this->id;
        $record->unread = $this->unread[$userid];

        $DB->insert_record('local_mail_index', $record);
    }

    private function delete_index($userid, $type=false, $itemid=0) {
        global $DB;

        $conditions = array();
        $conditions['messageid'] = $this->id;
        $conditions['userid'] = $userid;
        if ($type) {
            $conditions['type'] = $type;
            $conditions['item'] = $itemid;
            $conditions['time'] = $this->time;
        }
        $DB->delete_records('local_mail_index', $conditions);
    }

    private function has_user($userid) {
        return isset($this->users[$userid]);
    }

    private static function normalize_text($text) {
        // Replaces non-alphanumeric characters with a space.
        return trim(preg_replace('/(*UTF8)[^\p{L}\p{N}]+/', ' ', $text));
    }

    private function set_references($message) {
        global $DB;

        $this->refs = array_merge(array($message->id), $message->refs);

        $DB->delete_records('local_mail_message_refs', array('messageid' => $this->id));

        foreach ($this->refs as $ref) {
            $record = new stdClass;
            $record->messageid = $this->id;
            $record->reference = $ref;
            $DB->insert_record('local_mail_message_refs', $record);
        }
    }
}
