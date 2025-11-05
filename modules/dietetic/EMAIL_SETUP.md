# Email Notifications Setup for Dietetic Module

## Prerequisites

The Dietetic module uses Perfex CRM's built-in email system. Email notifications will only work if Perfex CRM is properly configured to send emails.

## 1. Configure Perfex CRM Email Settings

### Step 1: Access Email Settings
1. Login to Perfex CRM as Administrator
2. Go to **Setup** → **Settings** → **Email**

### Step 2: Configure SMTP
Choose one of these options:

#### Option A: Gmail SMTP
```
SMTP Host: smtp.gmail.com
SMTP Port: 587
SMTP Encryption: TLS
SMTP Username: your-email@gmail.com
SMTP Password: your-app-password (NOT your Gmail password!)
```

**Important**: For Gmail, you need to create an "App Password":
1. Go to your Google Account
2. Security → 2-Step Verification → App passwords
3. Generate a new app password for "Mail"
4. Use this password in Perfex

#### Option B: SendGrid
```
SMTP Host: smtp.sendgrid.net
SMTP Port: 587
SMTP Encryption: TLS
SMTP Username: apikey
SMTP Password: YOUR_SENDGRID_API_KEY
```

#### Option C: Mailgun
```
SMTP Host: smtp.mailgun.org
SMTP Port: 587
SMTP Encryption: TLS
SMTP Username: postmaster@your-domain.mailgun.org
SMTP Password: YOUR_MAILGUN_PASSWORD
```

### Step 3: Set Email From Address
```
From Email: noreply@yourdomain.com
From Name: Diet Senegal (or your company name)
```

### Step 4: Test Email
1. In Perfex email settings, scroll to bottom
2. Click "Send Test Email"
3. Enter your email address
4. Check if you receive the test email

## 2. Dietetic Module Email Features

The module sends emails for:

### Consultation Reminders
- **When**: 24 hours before scheduled consultation
- **Sent To**: Patient (client)
- **Content**: Consultation date, time, type, dietitian name

### Program Assignment
- **When**: New dietetic program is created for a patient
- **Sent To**: Patient (client)
- **Content**: Program details, start date, objectives

### Measurement Milestones
- **When**: Patient reaches target weight
- **Sent To**: Patient and dietitian
- **Content**: Congratulations message with progress summary

## 3. Enable Email Notifications in Dietetic Module

### Via Settings Page
1. Go to **Dietetic** → **Settings**
2. Enable these options:
   - ✅ Send consultation reminders
   - ✅ Send program notifications
   - ✅ Send milestone alerts
3. Click "Save Settings"

### Cron Job for Scheduled Emails
The module uses Perfex's cron system to send scheduled reminders.

**Verify cron is running**:
1. Go to **Setup** → **Settings** → **Cron Job**
2. Check "Last Cron Run" timestamp
3. Should run every 5-10 minutes

**If cron is not working**, add this to your server crontab:
```bash
*/5 * * * * php /path/to/perfex/index.php cron/index >/dev/null 2>&1
```

Replace `/path/to/perfex` with your actual Perfex installation path.

## 4. Troubleshooting Email Issues

### Emails Not Sending

**Check 1: SMTP Settings**
- Go to Setup → Settings → Email
- Click "Send Test Email"
- If test fails, SMTP settings are wrong

**Check 2: Email Queue**
- Check `tblemailtemplates` table in database
- Check Perfex logs in `application/logs/`

**Check 3: Firewall**
- Ensure server can connect to SMTP port (587 or 465)
- Test with: `telnet smtp.gmail.com 587`

**Check 4: Cron Job**
- Verify cron is running
- Check last cron run time in Settings

### Common Errors

**"SMTP connect() failed"**
- Wrong SMTP host or port
- Firewall blocking outbound SMTP
- Check with hosting provider

**"Invalid credentials"**
- Wrong SMTP username/password
- For Gmail: must use App Password, not regular password

**"Connection timed out"**
- Server firewall blocking port 587/465
- Contact hosting provider to whitelist SMTP ports

## 5. Testing Email Notifications

### Test Consultation Reminder
1. Create a consultation for tomorrow
2. Wait for next cron run (max 10 minutes)
3. Check if patient receives email
4. Check `application/logs/` for errors

### Test Program Notification
1. Create a new program for a patient
2. Email should send immediately
3. Check patient's email

### Check Email Logs
```sql
-- View recent emails in database
SELECT * FROM tblemailtemplates
ORDER BY datecreated DESC
LIMIT 10;

-- Check sent emails
SELECT * FROM tblemailslog
ORDER BY date DESC
LIMIT 20;
```

## 6. SMS Notifications (Bonus)

The module supports SMS reminders via LAM SMS API (Senegal).

### Configure LAM SMS API
1. Go to **Dietetic** → **Settings**
2. Enter:
   - LAM API URL: `https://api.lamsms.sn/send`
   - LAM API Key: (your LAM API key)
   - LAM Sender: (your sender ID)
3. Enable "Send SMS Reminders"
4. Save settings

### How It Works
- SMS sent 24h before consultation
- Also sent for urgent notifications
- Uses Senegal phone format

## 7. Email Templates

Email templates are in:
```
modules/dietetic/language/english/dietetic_lang.php
modules/dietetic/language/french/dietetic_lang.php
```

To customize email content, edit these language files.

## Support

If emails still don't work after following this guide:

1. **Check Perfex email settings** (most common issue)
2. **Check server firewall/SMTP ports**
3. **Check application logs** in `application/logs/`
4. **Contact hosting provider** about SMTP access

Email notifications depend 100% on Perfex CRM's email system being configured correctly. The Dietetic module simply uses Perfex's email functions.
