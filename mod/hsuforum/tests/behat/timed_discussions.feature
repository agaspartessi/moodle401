@mod @mod_hsuforum
Feature: In Open Forums users can choose to set start and end time for display of their discussions
  In order to temporarly hide discussions to students
  As a teacher
  I need to set a discussion time start and time end

  Scenario: Student should not see the tooltip or the discussion
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    And I log in as "admin"
    And the following config values are set as admin:
      | enabletimedposts | 1 | hsuforum |
    And I am on "Course 1" course homepage with editing mode on
    And the following "activity" exists:
      | activity          | hsuforum                |
      | name              | Test forum name         |
      | course            | C1                      |
      | intro             | Test forum description  |
      | section           | 1                       |
    And I am on the "Test forum name" Activity page
    And I add a new discussion to "Test forum name" Open Forum with:
      | Subject | Discussion 1 |
      | Message | Discussion contents 1, first message |
    And I add a new discussion to "Test forum name" Open Forum with:
      | Subject          | Discussion 2 timed not visible       |
      | Message          | Discussion contents 2, first message |
      | timeend[enabled] | 1 |
      | timeend[year]    | 2014 |
    And I add a new discussion to "Test forum name" Open Forum with:
      | Subject            | Discussion 3 timed visible now       |
      | Message            | Discussion contents 3, first message |
      | timestart[enabled] | 1 |
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    And I should see "Discussion 2 timed"
    And I should see "Discussion 3 timed"
    And ".timedpost" "css_element" should exist
    And I log out
    And I log in as "student1"
    When I am on "Course 1" course homepage
    And I follow "Test forum name"
    Then I should see "Discussion 1"
    And I should not see "Discussion 2 timed"
    And ".timedpost" "css_element" should not exist
    And I should see "Discussion 3 timed"
