FocusTrack is a web-based personal management system designed for students to manage their daily routines efficiently.The student tracker application for tracking students' behaviors based on the money usage, daily journal, exercise, and habits. There would be a complete web application with server-side scripting and PHP language. In addition, the application also design with good UI   design and well interaction. The website is operated with the MySQL Database. It is integrate with four modules and would be listed below.

1. Exercise Tracker Module

This module allows users to record, view, edit, and delete their exercise activities.
It shows a list of all exercises and their corresponding history records.
When a user completes an exercise, the system automatically inserts a record into the exercise history table.
The View Exercise List page displays all exercises with search, edit, and delete options.
The Exercise History page shows past workout sessions, limited to 10 per page.
It includes error handling, so any failed database updates will display clear error messages.

Purpose: Helps students maintain a consistent fitness routine and monitor their progress over time.

2. Daily Journal Module

This module works as a personal diary where users can document their daily experiences, emotions, and reflections.
Each journal entry includes a date, mood, and entry content.
Users can add, view, edit, and delete entries through well-structured forms.
The system ensures data validation, such as checking empty fields, correct date format, and valid mood selection.
Security features include session checks, ownership validation, and input sanitization using htmlspecialchars() to prevent attacks like XSS.

Purpose: Helps students record their feelings and thoughts, promoting self-reflection and emotional well-being.

3. Money Tracker Module

This module assists users in managing their finances by tracking income and expenses.
It allows adding transactions with fields for amount, category, type (income/expense), description, and date.
Users can sort and filter records by type or amount, and see their total balance (income − expenses).
It includes options to edit or delete transactions with confirmation prompts to prevent accidental deletions.
The interface provides real-time feedback with success or failure messages after every operation.

Purpose: Encourages students to manage their spending habits and gain financial awareness.

4. Habit Tracker Module

This module helps users build and maintain daily habits or track tasks scheduled for specific dates.
It displays Today’s Habits, automatically inserting records for the current day with a “Not Done” status if not yet updated.
Users can mark habits as Done or Not Done, and add remarks.
The Habit List shows all habits created, with options to filter, sort, edit, or delete.
The Habit History keeps track of completed or missed habits, preserving past records even if a habit is later deleted.

Purpose: Motivates students to develop positive daily habits and track long-term consistency.
