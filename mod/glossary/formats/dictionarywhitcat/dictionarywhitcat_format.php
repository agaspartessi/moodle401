<?php

function glossary_show_entry_dictionarywhitcat($course, $cm, $glossary, $entry, $mode='', $hook='', $printicons=1, $aliases=true) {

    global $CFG, $USER, $DB;

    if (isset ($entry->entryid))
    	$categories = $DB->get_record('glossary_entries_categories', array('entryid'=>$entry->entryid));
    else
	$categories = $DB->get_record('glossary_entries_categories', array('entryid'=>$entry->id));

    if (isset($categories->categoryid)){
    	$category = $DB->get_record('glossary_categories', array('id'=>$categories->categoryid));
	$catname = $category->name;}
    else{
	$catname = get_string('notcategorised', 'glossary');}
	

    echo '<table class="glossarypost dictionarywhitcat" cellspacing="0">';
    echo '<tr valign="top">';
    echo '<td class="entry">';
    glossary_print_entry_approval($cm, $entry, $mode);
    echo '<div class="cat">'.$catname.'</div> ';
    echo '<div class="concept">';
    glossary_print_entry_concept($entry);
    echo '</div> ';
    glossary_print_entry_definition($entry, $glossary, $cm);
    glossary_print_entry_attachment($entry, $cm, 'html');
    echo '</td></tr>';
    echo '<tr valign="top"><td class="entrylowersection">';
    glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $mode, $hook, $printicons, $aliases);
    echo '</td>';
    echo '</tr>';
    echo "</table>\n";
}

function glossary_print_entry_dictionarywhitcat($course, $cm, $glossary, $entry, $mode='', $hook='', $printicons=1) {

    //The print view for this format is exactly the normal view, so we use it

    //Take out autolinking in definitions in print view
    $entry->definition = '<span class="nolink">'.$entry->definition.'</span>';

    //Call to view function (without icons, ratings and aliases) and return its result
    return glossary_show_entry_dictionarywhitcat($course, $cm, $glossary, $entry, $mode, $hook, false, false, false);
}


