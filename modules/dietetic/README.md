# Perfex CRM Dietetic Module

A comprehensive dietetic management system for Perfex CRM, designed to connect dietitians with their patients and manage complete nutrition programs.

## 📋 Features

### Patient Management
- **Complete Patient Profiles**: Link to existing Perfex clients with detailed health information
- **Medical History**: Track medical conditions, allergies, medications, and lifestyle
- **Body Measurements**: Record weight, BMI, body fat, muscle mass, and body measurements
- **Weight Evolution Charts**: Visual tracking of progress over time
- **Patient Assignment**: Assign patients to specific dietitians (staff members)

### Consultations
- **Appointment Scheduling**: Create and manage consultation appointments
- **Calendar View**: FullCalendar integration for viewing scheduled appointments
- **Consultation Types**: Initial, follow-up, emergency, online, in-person
- **Notes & Observations**: Record detailed consultation notes and recommendations
- **Satisfaction Tracking**: Rate patient satisfaction after each consultation
- **Automatic Reminders**: Email/SMS reminders before appointments

### Dietetic Programs
- **Custom Programs**: Create personalized nutrition programs for each patient
- **Nutritional Targets**: Set daily goals for calories, protein, carbs, fats, and fiber
- **Program Duration**: Define start and end dates with renewal reminders
- **Status Tracking**: Active, completed, or cancelled programs
- **Multiple Programs**: Support for multiple programs per patient

### Meal Plans
- **Weekly Meal Plans**: Create detailed weekly meal plans within programs
- **Daily Meals**: Support for breakfast, snacks, lunch, dinner
- **Meal Composition**: Add multiple foods to each meal with quantities
- **Nutritional Calculations**: Automatic calculation of calories and macros
- **Meal Instructions**: Add preparation and consumption instructions
- **PDF Generation**: Download printable meal plans with full nutritional breakdown
- **Plan Duplication**: Quickly duplicate meal plans for subsequent weeks

### Food Database
- **Comprehensive Database**: Extensive food library with nutritional values
- **Nutritional Information**: Calories, protein, carbs, fats, fiber, sugar, sodium
- **Food Categories**: Vegetables, fruits, proteins, grains, dairy, fats, beverages, snacks
- **Multilingual**: English and French food names
- **Allergen Tracking**: Record allergens for each food
- **Import/Export**: CSV import and export functionality
- **Search**: Quick food search functionality

### Client Portal
- **Program Access**: Clients can view their active dietetic programs
- **Meal Plans**: View and download meal plans as PDF
- **Measurements**: Clients can add their own measurements (if enabled)
- **Weight Progress**: Visual charts showing weight evolution
- **Consultations**: View upcoming and past consultations
- **Dashboard**: Overview of current program, progress, and next steps

### Reminders & Notifications
- **Automated Reminders**: Cron-based reminder system
- **Appointment Reminders**: Sent X hours before consultation
- **Program Renewal**: Remind patients about program expiration
- **SMS Integration**: LAM SMS API integration for text messages
- **Email Notifications**: Perfex email template integration

### Reports & Analytics
- **Dashboard KPIs**: Patient count, active programs, consultations, satisfaction
- **Weight Evolution**: Charts showing patient progress
- **Consultation Analytics**: Types, completion rates, satisfaction scores
- **Export Functionality**: Export patients, consultations, programs to CSV
- **Patient Growth**: Historical data on new patient acquisition

### Permissions
- **Role-Based Access**: Full integration with Perfex permission system
- **View, Create, Edit, Delete**: Granular permissions for each action
- **Staff Restrictions**: Non-admin staff see only their own patients
- **Client Portal Access**: Automatic access for patients

## 🚀 Installation

### Requirements
- Perfex CRM 2.3.0 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Installation Steps

1. **Download the Module**
   ```bash
   # Download or copy the dietetic folder
   ```

2. **Upload to Perfex**
   - Extract the `dietetic` folder
   - Upload to `/modules/` directory in your Perfex installation
   - Final path should be: `/modules/dietetic/`

3. **Activate the Module**
   - Log in to Perfex CRM as Administrator
   - Navigate to: `Setup > Modules`
   - Find "Dietetic Management" in the list
   - Click "Activate"

4. **Verify Installation**
   - The module will automatically:
     - Create all necessary database tables
     - Insert default settings
     - Load sample food data
     - Create upload directories
   - Check for "Dietetic" menu item in the admin sidebar

5. **Set Permissions**
   - Navigate to: `Setup > Roles`
   - Configure permissions for each staff role:
     - View (Global): View all dietetic data
     - Create: Add new patients, consultations, programs
     - Edit: Modify existing records
     - Delete: Remove records

## ⚙️ Configuration

### Module Settings

Navigate to: `Dietetic > Settings`

**SMS Integration (LAM API)**
- `LAM API URL`: Your LAM SMS API endpoint
- `LAM API Key`: Your LAM API authentication key
- `SMS Sender Name`: Name displayed as sender

**Reminders**
- `Appointment Reminder Hours`: Hours before consultation to send reminder (default: 24)
- `Enable Meal Reminders`: Toggle meal time reminders
- `Enable Measurement Reminders`: Toggle measurement reminders
- `Renewal Reminder Days`: Days before program end to send renewal notice (default: 7)

**General**
- `Default Consultation Duration`: Default duration in minutes (default: 60)
- `Enable Client Booking`: Allow clients to book appointments from portal
- `Enable Client Measurements`: Allow clients to add measurements

**PDF Generation**
- `PDF Logo Path`: Path to logo for meal plan PDFs (optional)

### Cron Job Setup

For automated reminders, add this to your cron:

```bash
# Run every 15 minutes
*/15 * * * * php /path/to/perfex/index.php cron/index
```

The Perfex cron will automatically trigger dietetic reminder processing.

**Test Cron Manually** (Admin only):
```
https://yourdomain.com/admin/dietetic/test_cron
```

## 📖 Usage Guide

### Creating a Patient

1. Navigate to: `Dietetic > Patients > New Patient`
2. Select an existing Perfex client
3. Assign a dietitian (staff member)
4. Fill in patient information:
   - Gender, birth date, contact info
   - Medical history (conditions, allergies, medications)
   - Lifestyle and dietary preferences
   - Activity level
   - Initial weight, target weight, height
5. Save

The BMI will be calculated automatically, and an initial measurement will be recorded.

### Adding a Consultation

1. Navigate to: `Dietetic > Consultations > New Consultation`
2. Select patient
3. Set date/time and duration
4. Choose consultation type
5. Add location and reason
6. Save

An automatic reminder will be created based on your settings.

### Creating a Dietetic Program

1. Navigate to: `Dietetic > Programs > New Program`
2. Select patient
3. Set program details:
   - Program name and description
   - Start and end dates
   - Objective
   - Daily nutritional targets (calories, protein, carbs, fats)
   - Meal count per day
   - Instructions
4. Save

### Building a Meal Plan

1. Open a program
2. Click "Add Meal Plan"
3. Set week number and plan name
4. For each day and meal type:
   - Add meal name and time
   - Search and add foods
   - Specify quantities
   - Add preparation instructions
5. Save

**Nutritional totals are calculated automatically.**

### Generating PDF

1. Open a meal plan
2. Click "Generate PDF"
3. PDF will be created and downloaded automatically
4. PDF path is saved and accessible to client in portal

### Using the Food Database

**Add Foods Manually:**
- Navigate to: `Dietetic > Food Database > New Food`
- Enter food name (English and French)
- Select category
- Enter serving size and nutritional values
- Save

**Import Foods from CSV:**
- Navigate to: `Dietetic > Food Database > Import`
- Prepare CSV file with columns:
  ```
  Food Name, Food Name FR, Category, Serving Size, Serving Unit,
  Calories, Protein, Carbs, Fats, Fiber, Sugar, Sodium
  ```
- Upload and import

### Client Portal Access

Clients automatically get access when they have a patient record.

**Portal Features:**
- Dashboard with program overview
- View active meal plans
- Download meal plan PDFs
- Add measurements (if enabled)
- View weight progress chart
- View consultation history

**Access URL:**
```
https://yourdomain.com/clients/dietetic/portal
```

## 🗂️ Module Structure

```
dietetic/
├── assets/
│   ├── css/
│   │   ├── dietetic.css
│   │   └── dietetic_portal.css
│   └── js/
│       ├── dietetic.js
│       └── dietetic_portal.js
├── controllers/
│   ├── Dietetic.php (Dashboard, settings)
│   ├── Patients.php
│   ├── Consultations.php
│   ├── Programs.php
│   ├── Foods.php
│   └── Portal.php (Client-facing)
├── models/
│   ├── Dietetic_patients_model.php
│   ├── Dietetic_measurements_model.php
│   ├── Dietetic_consultations_model.php
│   ├── Dietetic_programs_model.php
│   ├── Dietetic_meal_plans_model.php
│   ├── Dietetic_foods_model.php
│   └── Dietetic_reminders_model.php
├── views/
│   ├── admin/ (Admin views)
│   ├── portal/ (Client portal views)
│   └── pdf/ (PDF templates)
├── helpers/
│   └── dietetic_helper.php
├── libraries/
│   └── Dietetic_pdf.php
├── language/
│   ├── english/
│   │   └── dietetic_lang.php
│   └── french/
│       └── dietetic_lang.php
├── dietetic.php (Module registration)
├── install.php
├── install.sql
├── uninstall.sql
├── sample_data.sql
└── README.md
```

## 🗃️ Database Tables

- `tbldietic_patients` - Patient profiles
- `tbldietic_measurements` - Body measurements
- `tbldietic_consultations` - Consultation appointments
- `tbldietic_programs` - Dietetic programs
- `tbldietic_meal_plans` - Weekly meal plans
- `tbldietic_meals` - Individual meals
- `tbldietic_meal_foods` - Foods in meals
- `tbldietic_foods` - Food database
- `tbldietic_reminders` - Automated reminders
- `tbldietic_documents` - Uploaded documents
- `tbldietic_settings` - Module settings

## 🔧 Troubleshooting

### Module doesn't appear after activation
- Check file permissions on `/modules/dietetic/`
- Verify `install.sql` executed properly
- Check PHP error logs

### Reminders not sending
- Verify cron job is configured
- Test manually via: `/admin/dietetic/test_cron`
- Check LAM SMS API credentials in settings
- Verify SMTP settings in Perfex

### PDF generation fails
- Check write permissions on `/modules/dietetic/uploads/`
- Verify PDF library is loaded
- Check PHP memory limit

### Foods not appearing
- Verify `sample_data.sql` was loaded
- Check `is_active = 1` in food database
- Clear browser cache

## 📝 Support & Development

**Version:** 1.0.0
**Author:** Perfex CRM
**Requires:** Perfex CRM 2.3.0+

### Feature Requests
Submit feature requests via your Perfex CRM support channel.

### Bug Reports
Include:
- Perfex CRM version
- PHP version
- Detailed error message
- Steps to reproduce

## 📄 License

This module is proprietary software developed for Perfex CRM.

---

**Ready to use!** After installation, navigate to the Dietetic menu to start managing your dietetic practice.
