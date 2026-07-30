# eHostel - Feature Documentation

## Overview

The **eHostel Online Hostel Management System** is a web-based application developed to simplify hostel management in a university. The system provides separate interfaces for **Students** and **Administrators**, allowing each user to perform tasks based on their assigned role.

The primary purpose of the system is to digitize hostel operations such as student registration, hostel applications, room allocation, occupancy tracking, and announcement management.

---

# Student Features

Students are the primary users of the system. They can create an account, apply for hostel accommodation, manage their personal information, and stay informed about hostel-related updates.

---

# 1. Student Registration

## Description

Student Registration allows new students to create an account in the hostel management system. Every student must register before accessing any other feature.

## How It Works

* The student opens the registration page.
* The student fills in all required personal information.
* The system checks whether the student has already registered.
* If the information is valid, a new account is created.
* The student can then log in using the registered credentials.

## Options Available

* Create a new account
* Enter personal details
* Create a secure password
* Submit registration
* Cancel registration

---

# 2. Student Login

## Description

The login feature authenticates students and allows them to access their personal dashboard securely.

## How It Works

* The student enters their email or student ID and password.
* The system verifies the credentials.
* If the information is correct, the student is redirected to the dashboard.
* If incorrect, an error message is displayed.

## Options Available

* Login
* Remember credentials (optional)
* View error messages for invalid login
* Navigate to registration page

---

# 3. Student Logout

## Description

Logout safely ends the student's session and prevents unauthorized access.

## How It Works

* The student clicks the Logout button.
* The system removes the active session.
* The student is redirected to the login page.

## Options Available

* Logout from the system
* Return to login page

---

# 4. Student Dashboard

## Description

The dashboard is the student's home page after logging in. It provides quick access to important hostel information.

## How It Works

The dashboard displays important information such as:

* Application status
* Allocated room details
* Latest announcements
* Hostel information

Students can navigate to all available features from this page.

## Options Available

* View application status
* View allocated room
* Open profile
* Apply for hostel
* Read announcements
* Logout

---

# 5. Student Profile Management

## Description

Students can view and update their personal information whenever necessary.

## How It Works

The system displays the student's profile information.

Students can update editable fields while permanent records remain protected.

## Options Available

* View profile
* Edit phone number
* Edit email
* Edit address
* Save changes
* Cancel changes

---

# 6. Hostel Application

## Description

Students can request hostel accommodation by submitting an online application.

## How It Works

* Student opens the application form.
* Required information is entered.
* The application is submitted.
* The system stores the application.
* The application status becomes **Pending** until reviewed by an administrator.

## Options Available

* Apply for hostel
* Enter application information
* Submit application
* Cancel application

---

# 7. View Application Status

## Description

Students can monitor the progress of their hostel application.

## How It Works

The system displays the current application status.

Possible statuses include:

* Pending
* Approved
* Rejected

If approved, room allocation information becomes visible.

## Options Available

* View current status
* View approval details
* View assigned room information

---

# 8. View Room Allocation

## Description

Students can view the room assigned by the administrator.

## How It Works

After approval, the system displays:

* Hostel Name
* Room Number
* Bed Number
* Floor
* Allocation Date

Students can only view this information.

## Options Available

* View room details
* View hostel information

---

# 9. View Room Availability

## Description

Students can check available rooms before applying.

## How It Works

The system displays every room together with its occupancy status.

Each room shows:

* Room Number
* Capacity
* Occupied Beds
* Vacant Beds
* Availability Status

Students cannot reserve rooms directly.

## Options Available

* View available rooms
* View room occupancy
* View room status

---

# 10. View Announcements

## Description

Students receive important information from hostel administrators through announcements.

## How It Works

Announcements are displayed in chronological order.

Students can read but cannot edit or remove announcements.

## Options Available

* View announcement list
* Read announcement details

---

# Administrator Features

Administrators manage every aspect of the hostel system.

---

# 1. Administrator Login

## Description

Allows administrators to securely access administrative functions.

## How It Works

The administrator enters valid credentials.

After successful authentication, the administrator dashboard is displayed.

## Options Available

* Login
* Logout

---

# 2. Administrator Dashboard

## Description

The administrator dashboard provides a complete overview of hostel activities.

## How It Works

The dashboard displays important statistics including:

* Total students
* Pending applications
* Approved applications
* Occupied rooms
* Vacant rooms
* Recent announcements

## Options Available

* Open student management
* Open room management
* Review applications
* Publish announcements

---

# 3. Student Management

## Description

Administrators manage all student records stored in the system.

## How It Works

Administrators can search for students and manage their information.

## Options Available

* View all students
* Search students
* View student details
* Edit student information
* Delete student records

---

# 4. Hostel Application Management

## Description

Administrators review all hostel applications submitted by students.

## How It Works

Applications are displayed with their current status.

The administrator reviews each application and decides whether to approve or reject it.

## Options Available

* View applications
* Approve application
* Reject application
* Search applications

---

# 5. Hostel Management

## Description

Allows administrators to manage hostel buildings available in the university.

## How It Works

Administrators maintain hostel information.

## Options Available

* Add hostel
* Edit hostel
* Delete hostel
* View hostel list

---

# 6. Room Management

## Description

Administrators maintain all hostel rooms.

## How It Works

Each room is registered with information such as room number, capacity and hostel building.

## Options Available

* Add room
* Edit room
* Delete room
* View room information
* Search rooms

---

# 7. Bed and Room Allocation

## Description

Administrators assign approved students to available rooms and beds.

## How It Works

* Select approved student
* Select available room
* Select available bed
* Save allocation

Once completed, the student's dashboard displays the assigned room.

## Options Available

* Allocate room
* Allocate bed
* Change allocation
* Remove allocation
* View allocation history

---

# 8. Occupancy Monitoring

## Description

Administrators can monitor hostel occupancy in real time.

## How It Works

The system automatically calculates:

* Total capacity
* Occupied beds
* Vacant beds
* Full rooms

This helps administrators manage room availability efficiently.

## Options Available

* View occupancy summary
* View available rooms
* View full rooms
* Search occupancy information

---

# 9. Search Student Records

## Description

Allows administrators to quickly locate student information.

## How It Works

Students can be searched using different criteria.

## Options Available

* Search by Student ID
* Search by Name
* Search by Email
* View student profile

---

# 10. Search Room Allocation

## Description

Allows administrators to locate room allocation information.

## How It Works

The administrator searches using room number or student information.

The system displays complete allocation details.

## Options Available

* Search room
* Search student allocation
* View room occupants
* View available beds

---

# 11. Announcement Management

## Description

Administrators communicate important hostel information through announcements.

## How It Works

Announcements are created and published for all students.

Students immediately see newly published announcements.

## Options Available

* Create announcement
* Edit announcement
* Delete announcement
* Publish announcement
* View announcement history

---

# System Features Available to All Users

## Secure Authentication

Every user must log in before accessing the system.

This ensures that only authorized users can access hostel information.

---

## Role-Based Access Control

The system assigns permissions according to the logged-in user's role.

### Student Permissions

* Register
* Login
* Manage personal profile
* Apply for hostel
* View application status
* View room allocation
* View room availability
* Read announcements
* Logout

### Administrator Permissions

* Login
* Manage students
* Approve applications
* Manage hostels
* Manage rooms
* Allocate beds
* Monitor occupancy
* Publish announcements
* Search records
* Logout

---

# Summary

The **eHostel Online Hostel Management System** provides a complete digital solution for managing university hostel operations. Students can register, apply for accommodation, manage their profiles, monitor their application status, view allocated rooms, and stay informed through announcements. Administrators can efficiently manage student records, approve applications, allocate rooms and beds, monitor hostel occupancy, maintain room information, and communicate important updates through announcements.

By separating student and administrator responsibilities, the system ensures secure, organized, and efficient hostel management while reducing manual paperwork and improving the overall user experience.


 

scrutinize this project /home/nimantha/code/uni/eHostel get idea about it, you can see it just simple university hostem management system with simple function now i want to redesing this whole website i mean change ui/ux the way it's look like so i have very sophisticated website with interesting ui/ux now i want to apply that style apt as this my university eHostel app so examine this /home/nimantha/code/uni/FURNIVIZ get idea about get idea how it desing ui/ux and how it look like the way it desing it after getting idea about apply that desing pattern my eHostel app same as FURNIVIz app i want to add picture to landing page use this for landing /home/nimantha/code/uni/eHostel/images/zMAnY.jpg and use this photo /home/nimantha/code/uni/eHostel/images thease are related to hostel room photo use those photo apt position and read .md file to fet idea about project /home/nimantha/code/uni/eHostel/reqire.md what kind of function's feature's that we are gonna enable to our user i want  you to apply change this web application look, redesing complete website the way it look and import this ehostel application we are not allow to use any framework or libraries or Api only can use php,html,css

and current project function and feature are limited so you have explore this project /home/nimantha/code/uni/hostel_mngment (2) to get idea about feature and explore this /home/nimantha/code/uni/eHostel/reqire.md file to so please along with redesing application you have to add feature's according this requirement this you can edit DB table also this project currently run on apache server in linux machine, do this step by step don't try apply all the change's at onces