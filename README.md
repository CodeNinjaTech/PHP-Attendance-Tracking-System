# Attendance Tracking System PHP Project

## Description

The final PHP project aims to implement a system for registration and attendance tracking for the Computer Programming School (Greek: ΣΠΗΥ). The system will cater to various roles with specific capabilities assigned to each role.

### Roles and Capabilities

#### Administrator/Education Desk
- Add, modify, or remove students
- Add, modify, or remove classes/series
- Add, modify, or remove students in classes
- Ability to add optional documents to document student absences
- Ability to modify attendance/absence records
- View pupil absences by:
  - Day (by class and by reason for absence)
  - Week (by department and by reason for absence)
  - Month (by section and by reason for absence)
  - Total series (by section and by reason for absence)
- View documentation for selected absences

#### Chief
- Enter student absences per day
- Enter reason for absence and attach documents to document student absences
- View an aggregated daily table prior to absence entry

### Reasons for Absence

Reasons for absence can include:
- Illness
- Exemption due to duty
- Regular leave of absence
- Other (expandable to include all types of leave students can take)

### Optional Features

Each status modification should be timestamped with the user who made the modification (the last modification).
