# Tawk.to Live Chat Setup Guide

Complete step-by-step guide to set up and customize Tawk.to live chat for Pecos River Trading Company.

**Last Updated**: November 18, 2025

---

## Quick Start (5 Minutes)

### Step 1: Sign Up for Tawk.to

1. Go to https://www.tawk.to
2. Click **"Sign up - It's Free!"**
3. Fill out registration:
   - Name: Your name
   - Email: your@email.com
   - Password: Create strong password
4. Click **"Get Started"**
5. Verify your email (check inbox)

### Step 2: Create Your Property (Website)

1. After login, you'll see "Add Property" screen
2. Enter your website details:
   - **Property Name**: Pecos River Trading Company
   - **Website URL**: http://localhost:8300 (or your actual domain)
3. Click **"Add Property"**

### Step 3: Get Your Widget Code

1. Dashboard → Administration → **Chat Widget**
2. You'll see code snippet like this:
   ```html
   <script type="text/javascript">
   var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
   (function(){
   var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
   s1.async=true;
   s1.src='https://embed.tawk.to/673bc1234560abcdef123456/1hj2k3l4m5';
   s1.charset='UTF-8';
   s1.setAttribute('crossorigin','*');
   s0.parentNode.insertBefore(s1,s0);
   })();
   </script>
   ```

3. **Copy** the Property ID and Widget ID from the URL:
   - Property ID: `673bc1234560abcdef123456`
   - Widget ID: `1hj2k3l4m5`

### Step 4: Update Footer.php

1. Open `C:\xampp\htdocs\PRT2\includes\footer.php`
2. Find line 158:
   ```php
   s1.src = 'https://embed.tawk.to/YOUR_PROPERTY_ID/YOUR_WIDGET_ID';
   ```
3. Replace with YOUR actual IDs:
   ```php
   s1.src = 'https://embed.tawk.to/673bc1234560abcdef123456/1hj2k3l4m5';
   ```
4. Save the file

### Step 5: Test It!

1. Open your website in browser: http://localhost:8300
2. You should see **chat widget** (bubble) in bottom-right corner
3. Click the bubble → Chat window opens
4. Type a test message
5. Go to https://dashboard.tawk.to → You should see the chat!

**That's it! Live chat is working! 🎉**

---

## Step 2: Customize Widget (Match PRT Branding)

### Widget Appearance

1. Go to **Administration → Chat Widget → Appearance**

2. **Widget Style**:
   - Widget Type: **Bubble**
   - Position: **Bottom Right**
   - Offset from bottom: **20px**
   - Offset from right: **20px**

3. **Colors** (Match PRT Red/Brown):
   - **Primary Color**: `#660000` (PRT Red)
   - **Bubble Color**: `#660000` (PRT Red)
   - **Text Color**: `#FFFFFF` (White)

4. **Widget Button**:
   - Button Text: `Need Help? Chat with us!`
   - Icon: Chat bubble icon (default)

5. **Chat Window**:
   - Window Title: `Pecos River Trading Company`
   - Greeting Message: `Howdy! How can we help you today?`

6. **Avatar**:
   - Upload company logo as agent avatar
   - Or use default Tawk.to avatar

7. Click **"Save Changes"**

### Pre-Chat Form (Optional)

1. Go to **Administration → Chat Widget → Pre-Chat Form**

2. **Enable Pre-Chat Form**: Toggle ON (if you want to collect info before chat)

3. **Fields**:
   - ✅ Name (Required)
   - ✅ Email (Required)
   - ❌ Phone (Optional)
   - ✅ Question (Required) - "How can we help you?"

4. **Welcome Message**:
   ```
   Welcome to Pecos River Trading Company!

   Please fill out this quick form and we'll connect you with an agent.
   ```

5. Click **"Save"**

**Recommendation**: Skip pre-chat form for faster customer experience. You can get name/email during the conversation.

### Offline Message Form

1. Go to **Administration → Chat Widget → Offline Message**

2. **Enable Offline Form**: Toggle ON

3. **Offline Message**:
   ```
   Our agents are currently offline.

   Leave us a message and we'll get back to you within 24 hours!
   ```

4. **Fields**:
   - ✅ Name (Required)
   - ✅ Email (Required)
   - ✅ Message (Required)

5. **Email Notifications**:
   - ✅ Send offline messages to: your@email.com
   - Add multiple emails (comma separated)

6. **Auto-Reply Email**:
   ```
   Subject: We received your message!

   Howdy!

   Thank you for contacting Pecos River Trading Company. We've received your message and will respond within 24 hours.

   In the meantime, check out our FAQ: http://localhost:8300/pages/faq.php

   Best regards,
   Pecos River Trading Company
   ```

7. Click **"Save"**

---

## Step 3: Set Up Agent Accounts

### Add Your First Agent

1. Go to **Administration → Agents**

2. Click **"Add Agent"** button

3. Fill out form:
   - **Name**: Sarah Johnson (or actual agent name)
   - **Email**: sarah@pecosrivertraders.com
   - **Role**:
     - **Administrator** (can manage settings)
     - **Agent** (can only chat)
     - **Monitor** (can only view)
   - **Departments**: General Support (create if needed)
   - **Display Name**: Sarah (what customers see)

4. Click **"Send Invitation"**

5. Agent receives email invitation:
   - Click link in email
   - Create password
   - Log in to dashboard

### Add Multiple Agents

Repeat the process for each team member:
- Agent 1: Sarah (Support) - Administrator
- Agent 2: Tom (Sales) - Agent
- Agent 3: Manager (View Only) - Monitor

### Configure Agent Routing

1. Go to **Administration → Routing**

2. **Distribution Method**:
   - **Round Robin**: Distribute evenly (RECOMMENDED)
   - **Broadcast**: All agents notified
   - **Manual**: Agents pick chats

3. **Max Conversations per Agent**: `5`

4. **Auto-Accept**: Toggle ON (agents auto-assigned chats)

5. Click **"Save"**

### Create Departments (Optional)

1. Go to **Administration → Departments**

2. Click **"Add Department"**

3. Create departments:
   - **Sales** (Product questions, purchases)
   - **Support** (Order status, technical issues)
   - **Returns** (Returns, exchanges, refunds)

4. Assign agents to departments

5. **Pre-Chat Routing**:
   - Let customers choose department before chat
   - Routes to appropriate agent

---

## Step 4: Configure Working Hours & Offline Messages

### Set Business Hours

1. Go to **Administration → Working Hours**

2. **Enable Working Hours**: Toggle ON

3. **Time Zone**: Select your timezone (e.g., America/Chicago for Texas)

4. **Set Hours**:
   ```
   Monday:    9:00 AM - 5:00 PM
   Tuesday:   9:00 AM - 5:00 PM
   Wednesday: 9:00 AM - 5:00 PM
   Thursday:  9:00 AM - 5:00 PM
   Friday:    9:00 AM - 5:00 PM
   Saturday:  10:00 AM - 3:00 PM (optional)
   Sunday:    CLOSED
   ```

5. **Offline Behavior**:
   - ✅ Show offline message form
   - ❌ Hide widget completely (not recommended)

6. Click **"Save"**

### Test Offline Mode

1. Set all agents to **Offline** status
2. Go to your website
3. Click chat widget
4. Should see offline message form instead of live chat
5. Submit test message
6. Check email - you should receive notification

---

## Step 5: Create Canned Responses (Quick Replies)

Canned responses save time for common questions.

### Set Up Canned Responses

1. Go to **Settings → Canned Responses**

2. Click **"Add Response"** for each:

#### Shipping Policy
- **Shortcut**: `/shipping`
- **Title**: Shipping Information
- **Message**:
  ```
  We offer standard shipping (3-5 business days) and expedited shipping (1-2 business days).

  Standard: $6.99 (FREE on orders over $75)
  Expedited: $14.99

  International shipping available to Canada and Mexico.
  ```

#### Return Policy
- **Shortcut**: `/returns`
- **Title**: Return Policy
- **Message**:
  ```
  Returns accepted within 30 days of delivery!

  Items must be unworn with original tags.
  Refunds issued to original payment method within 5-7 business days.

  Need to start a return? Visit: http://localhost:8300/policies/return-policy.php
  ```

#### Order Status
- **Shortcut**: `/orderstatus`
- **Title**: Check Order Status
- **Message**:
  ```
  To check your order status, please log in to your account:
  http://localhost:8300/auth/orders.php

  Or provide me with your order number and I'll look it up for you!
  ```

#### Product Availability
- **Shortcut**: `/stock`
- **Title**: Check Stock
- **Message**:
  ```
  Let me check our inventory for you! What product and size are you looking for?
  ```

#### Business Hours
- **Shortcut**: `/hours`
- **Title**: Business Hours
- **Message**:
  ```
  Our customer support hours:
  Monday-Friday: 9:00 AM - 5:00 PM CST
  Saturday: 10:00 AM - 3:00 PM CST
  Sunday: Closed

  Outside these hours, leave a message and we'll respond within 24 hours!
  ```

#### Greeting
- **Shortcut**: `/hello`
- **Title**: Friendly Greeting
- **Message**:
  ```
  Howdy! Welcome to Pecos River Trading Company! I'm here to help with any questions about our western wear and boots. What can I assist you with today?
  ```

3. **How Agents Use Them**:
   - In chat, type `/shipping` → Response auto-fills
   - Or click canned response menu → select response
   - Sends instantly

---

## Step 6: Install Agent Apps

### Desktop App (Recommended for Full-Time Agents)

**Windows/Mac/Linux:**
1. Go to https://www.tawk.to/downloads/
2. Download for your OS:
   - Windows: `tawk-desktop-setup.exe`
   - Mac: `tawk-desktop.dmg`
   - Linux: `tawk-desktop.AppImage`
3. Install and run
4. Log in with agent credentials
5. **Benefits**:
   - Desktop notifications
   - System tray icon
   - Always-on presence
   - Faster than web browser

### Mobile Apps (For On-the-Go Agents)

**iOS (iPhone/iPad):**
1. Open App Store
2. Search: **"Tawk.to"**
3. Install free app
4. Log in with agent credentials

**Android:**
1. Open Google Play Store
2. Search: **"Tawk.to"**
3. Install free app
4. Log in with agent credentials

**Benefits**:
- Push notifications on phone
- Respond to chats anywhere
- Perfect for small teams

---

## Step 7: Hide Chat on Specific Pages (Optional)

Some pages shouldn't have chat (checkout, payment, etc.)

### Hide on Checkout Page

**File**: `/cart/checkout.php`

Add at the very top:
```php
<?php
$hideTawkChat = true; // Hide chat on checkout
session_start();
require_once(__DIR__ . '/../includes/header.php');
?>
```

### Hide on Other Pages

Add `$hideTawkChat = true;` at top of:
- `/auth/login.php` (login page)
- `/auth/register.php` (registration)
- `/cart/process_order.php` (order processing)

**Note**: Chat is visible everywhere by default. Only hide on pages where it would be distracting.

---

## Step 8: Test Everything

### Test Checklist

- [ ] **Widget Appears**: Chat bubble visible bottom-right
- [ ] **Widget Opens**: Click bubble → chat window opens
- [ ] **Send Message**: Type test message as customer
- [ ] **Agent Receives**: Check dashboard.tawk.to → message appears
- [ ] **Agent Responds**: Reply from dashboard → customer sees it
- [ ] **Customer Info**: Agent sees customer name, email, loyalty tier
- [ ] **Offline Mode**: Set agents offline → offline form appears
- [ ] **Offline Message**: Submit offline message → email received
- [ ] **Canned Responses**: Agent types `/shipping` → response fills
- [ ] **Mobile App**: Test chat from agent mobile app
- [ ] **Hidden Pages**: Chat hidden on checkout page
- [ ] **Colors Match**: Widget uses PRT red (#660000)

---

## Customization Options

### Advanced: Proactive Chat Triggers

Automatically start conversations based on behavior:

1. Go to **Administration → Triggers**

2. **Example Triggers**:

   **Trigger 1: Exit Intent**
   - Name: "Exit Intent - Cart Page"
   - Condition: User about to leave cart page
   - Message: "Wait! Need help completing your order? We're here to help!"

   **Trigger 2: Time on Page**
   - Name: "Time on Product Page"
   - Condition: User on product page for 30 seconds
   - Message: "Looking for the perfect boots? I can help you find the right size and style!"

   **Trigger 3: Cart Value**
   - Name: "High Cart Value"
   - Condition: Cart total > $200
   - Message: "Thanks for shopping with us! Need any help with your order?"

3. Click **"Save Trigger"**

### Advanced: Tag Conversations

Help with analytics and reporting:

**During Chat**:
- Agent tags conversation: "Product Question", "Complaint", "Sale"
- Tags visible in reports

**Why Tag?**:
- Track common issues
- Measure agent performance
- Identify training needs

---

## Monitoring & Analytics

### View Reports

1. Go to **Reports** in dashboard

2. **Available Reports**:
   - **Chat Volume**: Messages per day/week/month
   - **Response Time**: How fast agents respond
   - **Satisfaction**: Customer ratings after chat
   - **Agent Performance**: Chats per agent, ratings, response times
   - **Busiest Hours**: When most chats happen

3. **Use Reports To**:
   - Schedule agents during busy hours
   - Identify top-performing agents
   - Find common customer issues
   - Improve response times

### Real-Time Monitoring

**Monitor Tab**:
- See all visitors browsing your site
- Pages they're viewing
- Time on site
- Cart contents (if integrated)
- Proactively start chats with visitors

---

## Troubleshooting

### Widget Not Showing

**Check**:
1. Property ID and Widget ID correct in footer.php?
2. Cache cleared? (Hard refresh: Ctrl+F5)
3. JavaScript errors in browser console? (F12)
4. Page has `$hideTawkChat = true`?

### Agents Not Getting Notifications

**Check**:
1. Agent status set to "Online"?
2. Browser notifications enabled?
3. Desktop app installed and running?
4. Email notifications enabled in settings?

### Customer Data Not Showing

**Check**:
1. Customer logged in? (Guest visitors won't have data)
2. Session variables set correctly?
3. Check browser console for JavaScript errors

### Offline Form Not Working

**Check**:
1. All agents offline?
2. Working hours configured?
3. Offline message form enabled in settings?
4. Email address correct in offline settings?

---

## Support & Resources

- **Tawk.to Help Center**: https://help.tawk.to
- **Community Forum**: https://community.tawk.to
- **Email Support**: support@tawk.to
- **Live Chat**: Available on tawk.to website

---

## Next Steps After Setup

1. **Train Your Agents**:
   - Give 15-minute training on dashboard
   - Practice responding to test chats
   - Review canned responses together

2. **Monitor First Week**:
   - Check chat volume daily
   - Review response times
   - Adjust working hours if needed
   - Add more canned responses based on common questions

3. **Optimize**:
   - Set up triggers after 2 weeks of data
   - Create more canned responses
   - Adjust agent schedules based on busy times

4. **Integrate Further** (Optional):
   - Set up webhooks to log chats in database
   - Use API to show chat history in admin panel
   - Connect with email system for follow-ups

---

**Document Version**: 1.0
**Last Updated**: November 18, 2025
**Author**: Development Team

**You're all set! Live chat is ready to improve customer service and boost sales! 🎉**
