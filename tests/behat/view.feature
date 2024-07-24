Feature: Course visibility and management

  Background:
    Given the following "users" exist:
      | username | password | firstname | lastname | email           |
      | admin    | Admin123!| Admin     | User     | admin@example.com |
      | student  | Student123! | Student   | User     | student@example.com |
    And the following "courses" exist:
      | fullname      | shortname | category |
      | Test Course 1 | TC1       | 1        |
      | Test Course 2 | TC2       | 1        |
    And the following "enrolments" exist:
      | user    | course        | role    |
      | student | Test Course 1 | student |

  Scenario: Admin can see all courses and manage them
    Given I log in as "admin" with password "Admin123!"
    When I navigate to "Home / Site administration / Courses / Manage courses and categories"
    Then I should see "Test Course 1"
    And I should see "Test Course 2"
    And I press "Create course"
    And I set the following fields to these values:
      | Short name | New Course |
      | Full name  | New Course |
    And I press "Save and return"
    Then I should see "New Course"
    When I follow "New Course"
    And I press "Edit course settings"
    And I set the following fields to these values:
      | Full name | Updated Course |
    And I press "Save and display"
    Then I should see "Updated Course"
    When I navigate to "Home / Site administration / Courses / Manage courses and categories"
    And I press "Delete" for "Updated Course"
    And I press "Continue"
    Then I should not see "Updated Course"

  Scenario: Student can only see enrolled courses
    Given I log in as "student" with password "Student123!"
    When I navigate to "Home"
    Then I should see "Test Course 1"
    And I should not see "Test Course 2"
    And I should not see "Create course"
    And I should not see "Edit course settings"
    And I should not see "Delete"
    