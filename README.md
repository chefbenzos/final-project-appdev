# final-project-appdev
Creating a Student Portal

# Student View
- Main Website (HTML, CSS, PHP)
  - Navigation Taskbar
  - User Profile
    - Main Profile 
      - Student Number
      - Last Name
      - First Name
      - Middle Initial
      - Personal Email
      - Institute Email
      - Citizenship
      - Curriculum Code
    - Enrollment Status
      - Year Level
      - Registration Status
      - Student Type
      - Scholarship 
  - Academic Holds
    - Inform the user about missing requirements 
  - Account Balance Summary
    - Records of payment throughout the year   
  - Admission Documents
    - Similar to Academic Holds, but shows the information in tabular form
  - Course Offerings
    - Show the schedules available for the specific course the user is in   
  - Grade Report
    - Lock user from viewing when balance is <50%
    - Use user's registered schedule and grades
  - Program Curriculum
    - Shows the subjects available for the user's program 
  - Schedule and Assessment
    - Shows user schedule, and tuition calculation
  - Request Documents
    - Dedicated page for requesting specific documents from the registrar 

# Teacher View
- Main Website (HTML, CSS, PHP)
  - Manage Available Courses
  - Manage Grades for Student Courses
  - Manage Attendance for every meeting
  - Manage Schedule

# Registrar View
- Main Website (HTML, CSS, PHP)
  - Manage remaining balance of student
  - Manage transactions
  - Manage students' requirements and requests
 
# Other Key Features
- Database
  - 3 Tables, one for Students, one for Subjects, and one for Schedules

- CSS
  - Main theme: FEU

# Specific Files Needed
- PHP
  - db.php, for managing database queries
  - add-student.php, for inputting new students into the database
  - manage-student.php, for managing the status of a student's requirements, payment, and grades
  - homepage.php, for the main features involving the webite
    - Can be split into multiple pages for specific features  
 
