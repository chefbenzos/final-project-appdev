#Current Progress

landing-page.php
    - Landing page
        > Site Title
        >Logo
        > Email input field
        > Password input field
        > Login
            if login
            -> if student? main-dashboard-student.php : (if teacher? main-dashboard-teacher.php: (if registrar? main-dashboard-registrar.php : display err msg))

main-dashboard-student.php
    - User Profile 
        > landing page of main-dashboard-student
        > should display available user info 
    - Academic Holds
        > should display the missing required documents
    - Account Balance Summary
        > display balance
    - Payment Records
        > display logs of payments (amount, reason, date, payment method)
    - Admission Documents
        > display list of Submitted, missing, and pending admission files. should contain live status update and in page file upload
    - Course Offerings
        > display course list
    - Grade Report
        > display dropdown options for completed year & semester
            -> display grade for respective year & sem
    - Program Curriculum
        > show all subjects of the enrolled curriculum (name, coursecode, units, etc...)
    - Schedule and Assessment
        > display currently enrolled schedule
    - Request Documents
        > display drop down for available documents
        > text input field for reason
        > submit to database (should have table for pending request table)

main-dashboard-teacher.php
    - Manage Available Courses
    - Manage Grades for Student Courses
    - Manage Attendance for every meeting
    - Manage Schedule

main-dashboard-registrar.php
    - Manage remaining balance of student
    - Manage transactions
    - Manage students' requirements and requests

FOR UPDATE:
1. Login and Logout needs to be changed to a functional one, currently it is only a redirect link to landing page
2. Static displays needs to be change to mySql query function to retrieve information
    > User Profile - Display actual information connected to the account
    > Academic Holds - Idk
    > Account Balance Summary - Display actual information connected to the account
    > Payment Records - Display actual payment logs connected to the account
    > Admission Documents - Fill out nalang kung need. Queries database sa status ng reqs
    > Course Offerings - Should this be static lang kaya or does it need to have the same sa solar
    > Grade Report - select term/year then display
