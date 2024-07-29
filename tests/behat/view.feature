@local @local_final
Feature: Course visibility and management

  Background:
    Given the following "courses" exist:
      | shortname | fullname |
      | TEST1     | Course1  |
      | TEST2     | Course2  |
    Given the following "users" exist:
      | username | firstname | lastname |
      | student1 | Student   | 1        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | TEST1  | student |
  Scenario: Student can only see enrolled courses
    Given I log in as "student1"
    When I navigate to "Home"
    Then I should see "TEST1"
    And I should not see "TEST2"
    And I should not see "Create course"
    And I should not see "Edit course settings"
    And I should not see "Delete"
