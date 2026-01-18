# CRM Features for Pecos River Traders Admin Panel

**Document Created**: November 28, 2025
**Purpose**: Comprehensive CRM (Customer Relationship Management) feature roadmap for the admin panel

---

## Table of Contents

1. [Overview](#overview)
2. [Customer 360 View](#customer-360-view)
3. [Customer Segmentation](#customer-segmentation)
4. [Communication Center](#communication-center)
5. [Marketing Automation](#marketing-automation)
6. [Sales Pipeline](#sales-pipeline)
7. [Customer Service & Support](#customer-service--support)
8. [Analytics & Insights](#analytics--insights)
9. [Loyalty Program Enhancement](#loyalty-program-enhancement)
10. [Task & Activity Management](#task--activity-management)
11. [Integration Hub](#integration-hub)
12. [Implementation Priority](#implementation-priority)
13. [Product Management Enhancements](#product-management-enhancements)
14. [Gap Analysis & Remaining Work](#gap-analysis--remaining-work)

---

## Overview

A CRM system transforms the admin panel from a simple order management tool into a comprehensive customer relationship platform. For an e-commerce business like Pecos River Traders, CRM features help:

- **Increase customer retention** through personalized engagement
- **Boost sales** via targeted marketing and upselling
- **Improve customer service** with complete interaction history
- **Make data-driven decisions** with customer analytics
- **Automate repetitive tasks** to save time

### Current Customer Features (Already Implemented)
- Basic customer list and details
- Order history per customer
- Loyalty points tracking
- Wishlist viewing
- Basic customer editing

### Proposed CRM Enhancements
The sections below detail features to transform the admin into a full CRM system.

---

## Customer 360 View

### Purpose
A single page showing everything about a customer at a glance.

### Features

#### 1. Enhanced Customer Profile Card
```
┌─────────────────────────────────────────────────────────────┐
│ [Avatar]  John Smith                    ⭐ Gold Member      │
│           john.smith@email.com          Customer Since: 2022│
│           (555) 123-4567                                    │
│                                                             │
│ [Message] [Call] [Email] [Add Note] [Edit]                 │
└─────────────────────────────────────────────────────────────┘
```

#### 2. Key Metrics Dashboard
| Metric | Description |
|--------|-------------|
| **Lifetime Value (LTV)** | Total revenue from this customer |
| **Average Order Value** | Mean purchase amount |
| **Purchase Frequency** | Orders per month/year |
| **Days Since Last Order** | Recency indicator |
| **Customer Health Score** | Algorithm-based engagement score (1-100) |
| **Churn Risk** | Low/Medium/High indicator |

#### 3. Activity Timeline
Chronological feed of all customer interactions:
- Orders placed
- Support tickets
- Email opens/clicks
- Website visits (if tracked)
- Reviews submitted
- Loyalty points earned/redeemed
- Account changes
- Notes added by staff

```
Timeline Example:
────────────────────────────────────────
📦 Nov 25, 2025 - Order #1234 placed ($156.00)
📧 Nov 20, 2025 - Opened "Black Friday Sale" email
💬 Nov 15, 2025 - Support ticket resolved
⭐ Nov 10, 2025 - Left 5-star review on Product X
🎁 Nov 1, 2025 - Redeemed 500 loyalty points
────────────────────────────────────────
```

#### 4. Customer Tags & Labels
- Custom tags: "VIP", "Wholesale", "Influencer", "Problem Customer"
- Auto-tags: "High Spender", "Frequent Buyer", "At Risk", "New Customer"
- Product affinity tags: "Boots Enthusiast", "Workwear Buyer"

#### 5. Related Contacts
- Link family members/business associates
- Shared shipping addresses
- Gift recipients

### Database Schema Addition
```sql
CREATE TABLE customer_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    color VARCHAR(7) DEFAULT '#6c757d',
    description TEXT,
    is_auto BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE customer_tag_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    tag_id INT NOT NULL,
    assigned_by INT,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (tag_id) REFERENCES customer_tags(id)
);

CREATE TABLE customer_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    activity_type ENUM('order', 'email', 'support', 'review', 'loyalty', 'login', 'note', 'other'),
    title VARCHAR(255),
    description TEXT,
    metadata JSON,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id)
);
```

---

## Customer Segmentation

### Purpose
Group customers based on behavior, demographics, and value for targeted marketing.

### Features

#### 1. Segment Builder
Visual interface to create customer segments:

```
┌─────────────────────────────────────────────────────────────┐
│ Segment: "High-Value Boot Buyers"                          │
├─────────────────────────────────────────────────────────────┤
│ Rules (AND):                                                │
│  ├─ Lifetime Value > $500                                   │
│  ├─ Has purchased from category "Boots"                     │
│  └─ Last order within 90 days                              │
├─────────────────────────────────────────────────────────────┤
│ Matching Customers: 234                    [Preview] [Save] │
└─────────────────────────────────────────────────────────────┘
```

#### 2. Pre-Built Segments
| Segment | Criteria |
|---------|----------|
| **New Customers** | First order in last 30 days |
| **VIP Customers** | LTV > $1000 OR Gold/Platinum tier |
| **At-Risk Customers** | No order in 90+ days, previously active |
| **Churned Customers** | No order in 180+ days |
| **One-Time Buyers** | Exactly 1 order, 60+ days ago |
| **Frequent Buyers** | 5+ orders in last 12 months |
| **High AOV** | Average order > $200 |
| **Discount Hunters** | 80%+ orders used coupons |
| **Email Engaged** | Opened email in last 30 days |
| **Abandoned Cart** | Has items in cart 24+ hours |

#### 3. RFM Analysis
Automatic segmentation based on:
- **R**ecency: When did they last purchase?
- **F**requency: How often do they purchase?
- **M**onetary: How much do they spend?

RFM Score Grid:
```
           Monetary
           Low    Med    High
         ┌──────┬──────┬──────┐
Freq High│ Dev  │ Loyal│ Champ│
     Med │ Prom │ Pot  │ Loyal│
     Low │ Hiber│ Risk │ BigSp│
         └──────┴──────┴──────┘

Champions: High R, High F, High M - Your best customers
Loyal: High F, High M - Consistent valuable buyers
Big Spenders: High M only - Large but infrequent
Promising: Medium across board - Nurture these
At Risk: Were good, declining activity
Hibernating: Low across board - Re-engagement needed
```

#### 4. Segment Actions
- Export segment to CSV
- Send email campaign to segment
- Apply discount code to segment
- Assign tag to segment members
- Create targeted ad audience

### Database Schema Addition
```sql
CREATE TABLE customer_segments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    rules JSON NOT NULL,
    is_dynamic BOOLEAN DEFAULT TRUE,
    customer_count INT DEFAULT 0,
    last_calculated TIMESTAMP,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE customer_segment_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    segment_id INT NOT NULL,
    customer_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_membership (segment_id, customer_id),
    FOREIGN KEY (segment_id) REFERENCES customer_segments(id),
    FOREIGN KEY (customer_id) REFERENCES users(id)
);
```

---

## Communication Center

### Purpose
Centralized hub for all customer communications.

### Features

#### 1. Unified Inbox
Single view of all customer messages:
- Email inquiries
- Contact form submissions
- Live chat transcripts
- SMS messages
- Social media messages (if integrated)

```
┌─────────────────────────────────────────────────────────────┐
│ 📥 Inbox (23 unread)                    [Compose] [Filter] │
├─────────────────────────────────────────────────────────────┤
│ ● John Smith - "Question about boot sizing"     2 min ago  │
│ ● Sarah Jones - "Order #1234 not received"      15 min ago │
│ ○ Mike Wilson - "Wholesale inquiry"             1 hour ago │
│ ○ Lisa Brown - "Return request"                 2 hours ago│
└─────────────────────────────────────────────────────────────┘
```

#### 2. Email Templates
Pre-built templates for common scenarios:

| Template Category | Examples |
|-------------------|----------|
| **Order Updates** | Confirmation, Shipped, Delivered, Delayed |
| **Customer Service** | Ticket received, Issue resolved, Follow-up |
| **Marketing** | Welcome series, Win-back, Birthday |
| **Transactional** | Password reset, Account update, Review request |
| **Personal** | Thank you note, VIP offer, Apology |

Template Variables:
```
{{customer.first_name}}
{{customer.last_name}}
{{customer.email}}
{{order.number}}
{{order.total}}
{{order.items}}
{{loyalty.points}}
{{loyalty.tier}}
{{store.name}}
{{store.phone}}
```

#### 3. Communication Log
Every email/message automatically logged to customer profile:
- Date/time sent
- Subject/content
- Delivery status
- Open/click tracking
- Staff member who sent

#### 4. Scheduled Messages
- Schedule emails for future delivery
- Timezone-aware sending
- Optimal send time suggestions

#### 5. Bulk Messaging
- Send to customer segments
- Mail merge with personalization
- A/B testing for subject lines
- Unsubscribe handling

### Database Schema Addition
```sql
CREATE TABLE email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category ENUM('order', 'service', 'marketing', 'transactional', 'personal'),
    subject VARCHAR(255) NOT NULL,
    body_html TEXT NOT NULL,
    body_text TEXT,
    variables JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE customer_communications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    type ENUM('email', 'sms', 'chat', 'phone', 'social', 'note'),
    direction ENUM('inbound', 'outbound'),
    subject VARCHAR(255),
    content TEXT,
    template_id INT,
    status ENUM('draft', 'scheduled', 'sent', 'delivered', 'opened', 'clicked', 'bounced', 'failed'),
    scheduled_at TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    opened_at TIMESTAMP NULL,
    clicked_at TIMESTAMP NULL,
    metadata JSON,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (template_id) REFERENCES email_templates(id)
);
```

---

## Marketing Automation

### Purpose
Automated campaigns triggered by customer behavior.

### Features

#### 1. Automation Workflows
Visual workflow builder for automated sequences:

```
┌─────────────────────────────────────────────────────────────┐
│ Workflow: "Abandoned Cart Recovery"                         │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  [TRIGGER: Cart Abandoned 1+ hours]                        │
│              │                                              │
│              ▼                                              │
│  [WAIT: 1 hour]                                            │
│              │                                              │
│              ▼                                              │
│  [SEND EMAIL: "Forgot Something?"]                         │
│              │                                              │
│        ┌─────┴─────┐                                       │
│        │           │                                        │
│   [Purchased?] [No Purchase]                               │
│        │           │                                        │
│        ▼           ▼                                        │
│   [END]    [WAIT: 24 hours]                                │
│                    │                                        │
│                    ▼                                        │
│            [SEND EMAIL: "10% Off Your Cart"]               │
│                    │                                        │
│                    ▼                                        │
│                  [END]                                      │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

#### 2. Pre-Built Automations

| Automation | Trigger | Actions |
|------------|---------|---------|
| **Welcome Series** | New account created | 3-email welcome sequence over 7 days |
| **Abandoned Cart** | Cart idle 1+ hours | 2 reminder emails with increasing discount |
| **Post-Purchase** | Order delivered | Thank you + review request |
| **Win-Back** | No order in 60 days | Re-engagement email with offer |
| **Birthday** | Customer birthday | Birthday discount email |
| **Browse Abandonment** | Viewed product, didn't buy | Product reminder email |
| **Review Follow-Up** | 14 days post-delivery | Review request email |
| **Loyalty Milestone** | Points threshold reached | Congratulations + redemption reminder |
| **VIP Upgrade** | Tier promotion | Welcome to new tier email |
| **Replenishment** | Consumable product (X days) | Reorder reminder |

#### 3. Trigger Types
- **Time-Based**: Specific date, recurring schedule
- **Behavior-Based**: Purchase, browse, cart, email interaction
- **Threshold-Based**: LTV reached, points earned, orders placed
- **Event-Based**: Birthday, anniversary, tier change
- **Manual**: Staff-triggered for individual or segment

#### 4. Automation Analytics
- Emails sent/delivered/opened/clicked
- Revenue attributed to automation
- Conversion rate per workflow
- A/B test results

### Database Schema Addition
```sql
CREATE TABLE automation_workflows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    trigger_type ENUM('time', 'behavior', 'threshold', 'event', 'manual'),
    trigger_config JSON NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    stats JSON,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE automation_steps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    workflow_id INT NOT NULL,
    step_order INT NOT NULL,
    step_type ENUM('email', 'sms', 'wait', 'condition', 'action', 'split'),
    config JSON NOT NULL,
    FOREIGN KEY (workflow_id) REFERENCES automation_workflows(id)
);

CREATE TABLE automation_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    workflow_id INT NOT NULL,
    customer_id INT NOT NULL,
    current_step INT,
    status ENUM('active', 'completed', 'exited', 'paused'),
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (workflow_id) REFERENCES automation_workflows(id),
    FOREIGN KEY (customer_id) REFERENCES users(id)
);
```

---

## Sales Pipeline

### Purpose
Track potential B2B customers, wholesale accounts, and high-value opportunities.

### Features

#### 1. Lead Management
For wholesale inquiries, B2B prospects, and special opportunities:

```
┌─────────────────────────────────────────────────────────────┐
│ Lead Pipeline                              [+ Add Lead]     │
├─────────────────────────────────────────────────────────────┤
│ New (5)    │ Contacted (3) │ Qualified (2) │ Won (8)       │
│ ─────────  │ ───────────── │ ───────────── │ ─────────     │
│ □ ABC Corp │ □ XYZ Store   │ □ Boot Barn   │ ✓ Western W   │
│ □ Ranch Co │ □ Shoe Depot  │ □ Cowboy Co   │ ✓ Rodeo Sup   │
│ □ Western  │ □ Retail Plus │               │ ✓ Farm Store  │
│ □ Farm Eq  │               │               │               │
│ □ Boot Wh  │               │               │               │
└─────────────────────────────────────────────────────────────┘
```

#### 2. Lead Fields
| Field | Description |
|-------|-------------|
| Company Name | Business name |
| Contact Person | Primary contact |
| Email/Phone | Contact info |
| Source | How they found us |
| Estimated Value | Potential revenue |
| Stage | Pipeline position |
| Assigned To | Staff member |
| Next Action | Follow-up task |
| Notes | Conversation history |

#### 3. Deal Stages
1. **New** - Just received inquiry
2. **Contacted** - Initial outreach made
3. **Qualified** - Confirmed as legitimate opportunity
4. **Proposal Sent** - Quote/pricing provided
5. **Negotiation** - Terms being discussed
6. **Won** - Deal closed successfully
7. **Lost** - Deal did not close

#### 4. Wholesale/B2B Features
- Volume discount tiers
- Net payment terms (Net 30, Net 60)
- Tax exemption certificates
- Custom pricing per account
- Minimum order requirements
- Dedicated account manager assignment

### Database Schema Addition
```sql
CREATE TABLE leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255),
    contact_name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50),
    source ENUM('website', 'referral', 'trade_show', 'cold_call', 'social', 'other'),
    stage ENUM('new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost') DEFAULT 'new',
    estimated_value DECIMAL(10,2),
    probability INT DEFAULT 50,
    assigned_to INT,
    next_action VARCHAR(255),
    next_action_date DATE,
    notes TEXT,
    converted_customer_id INT,
    lost_reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES admin_users(id)
);

CREATE TABLE lead_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    activity_type ENUM('call', 'email', 'meeting', 'note', 'stage_change'),
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id)
);
```

---

## Customer Service & Support

### Purpose
Ticketing system for customer issues and inquiries.

### Features

#### 1. Support Ticket System
```
┌─────────────────────────────────────────────────────────────┐
│ Support Tickets                    [+ New Ticket] [Filter]  │
├──────┬────────────┬──────────────┬──────────┬──────────────┤
│ #ID  │ Customer   │ Subject      │ Priority │ Status       │
├──────┼────────────┼──────────────┼──────────┼──────────────┤
│ 1045 │ John Smith │ Missing item │ 🔴 High  │ ⏳ Open      │
│ 1044 │ Mary Jones │ Size exchang │ 🟡 Med   │ 🔄 Pending   │
│ 1043 │ Bob Wilson │ Tracking ?   │ 🟢 Low   │ ✅ Resolved  │
└──────┴────────────┴──────────────┴──────────┴──────────────┘
```

#### 2. Ticket Properties
| Property | Options |
|----------|---------|
| **Status** | Open, In Progress, Pending Customer, Resolved, Closed |
| **Priority** | Low, Medium, High, Urgent |
| **Category** | Order Issue, Return/Exchange, Product Question, Shipping, Billing, Other |
| **Assigned To** | Staff member |
| **Related Order** | Link to order if applicable |

#### 3. Ticket Features
- Internal notes (not visible to customer)
- Threaded conversation history
- File attachments (images, receipts)
- Canned responses for common issues
- SLA tracking (response time, resolution time)
- Escalation rules
- Customer satisfaction rating

#### 4. Service Analytics
- Average response time
- Average resolution time
- Tickets by category
- Customer satisfaction score (CSAT)
- First contact resolution rate
- Tickets per staff member

#### 5. Knowledge Base Integration
- Link tickets to FAQ articles
- Suggest articles based on ticket content
- Track which articles resolve tickets

### Database Schema Addition
```sql
CREATE TABLE support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_number VARCHAR(20) UNIQUE,
    customer_id INT NOT NULL,
    order_id INT,
    subject VARCHAR(255) NOT NULL,
    category ENUM('order', 'return', 'product', 'shipping', 'billing', 'other'),
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('open', 'in_progress', 'pending_customer', 'resolved', 'closed') DEFAULT 'open',
    assigned_to INT,
    first_response_at TIMESTAMP NULL,
    resolved_at TIMESTAMP NULL,
    satisfaction_rating INT,
    satisfaction_comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (assigned_to) REFERENCES admin_users(id)
);

CREATE TABLE ticket_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    sender_type ENUM('customer', 'staff'),
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    is_internal BOOLEAN DEFAULT FALSE,
    attachments JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES support_tickets(id)
);
```

---

## Analytics & Insights

### Purpose
Deep customer analytics for data-driven decisions.

### Features

#### 1. Customer Analytics Dashboard
```
┌─────────────────────────────────────────────────────────────┐
│ Customer Analytics                    [Date Range: 30 days] │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Total Customers    New Customers    Returning Rate         │
│  ┌─────────────┐   ┌─────────────┐  ┌─────────────┐        │
│  │   12,456    │   │     234     │  │    42.3%    │        │
│  │   +5.2%     │   │   +12.1%    │  │    +3.1%    │        │
│  └─────────────┘   └─────────────┘  └─────────────┘        │
│                                                             │
│  Avg LTV          Avg Order Value   Churn Rate             │
│  ┌─────────────┐   ┌─────────────┐  ┌─────────────┐        │
│  │   $342.50   │   │   $87.25    │  │    8.2%     │        │
│  │   +8.7%     │   │   +2.3%     │  │   -1.5%     │        │
│  └─────────────┘   └─────────────┘  └─────────────┘        │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

#### 2. Key Metrics

| Metric | Calculation | Use Case |
|--------|-------------|----------|
| **Customer Lifetime Value (CLV)** | Total revenue from customer | Identify most valuable customers |
| **Customer Acquisition Cost (CAC)** | Marketing spend / new customers | ROI on marketing |
| **Churn Rate** | Lost customers / total customers | Retention health |
| **Net Promoter Score (NPS)** | Survey-based loyalty metric | Customer satisfaction |
| **Average Order Value (AOV)** | Revenue / orders | Upsell opportunity |
| **Purchase Frequency** | Orders / active customers | Engagement level |
| **Time Between Purchases** | Avg days between orders | Repurchase cycle |
| **First Purchase Conversion** | Buyers / visitors | Acquisition efficiency |
| **Repeat Purchase Rate** | Repeat buyers / total buyers | Loyalty indicator |

#### 3. Cohort Analysis
Track customer behavior by acquisition date:
```
                  Month After First Purchase
              M1    M2    M3    M4    M5    M6
Jan 2025    100%   45%   32%   28%   25%   23%
Feb 2025    100%   48%   35%   30%   27%   --
Mar 2025    100%   52%   38%   33%   --    --
Apr 2025    100%   50%   36%   --    --    --
May 2025    100%   47%   --    --    --    --
Jun 2025    100%   --    --    --    --    --
```

#### 4. Customer Journey Analytics
- Path to first purchase
- Most common product combinations
- Category affinity by customer segment
- Seasonal purchasing patterns
- Device/channel preferences

#### 5. Predictive Analytics
- Churn prediction model
- Next purchase prediction
- Product recommendation engine
- Optimal discount amount

### Database Schema Addition
```sql
CREATE TABLE customer_metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL UNIQUE,
    lifetime_value DECIMAL(10,2) DEFAULT 0,
    total_orders INT DEFAULT 0,
    avg_order_value DECIMAL(10,2) DEFAULT 0,
    first_order_date DATE,
    last_order_date DATE,
    days_since_last_order INT,
    purchase_frequency DECIMAL(5,2),
    rfm_recency_score INT,
    rfm_frequency_score INT,
    rfm_monetary_score INT,
    rfm_segment VARCHAR(50),
    churn_risk_score DECIMAL(3,2),
    health_score INT,
    calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id)
);

CREATE TABLE customer_events (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    session_id VARCHAR(100),
    event_type VARCHAR(50) NOT NULL,
    event_data JSON,
    page_url VARCHAR(500),
    referrer VARCHAR(500),
    device_type VARCHAR(20),
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_customer (customer_id),
    INDEX idx_event_type (event_type),
    INDEX idx_created (created_at)
);
```

---

## Loyalty Program Enhancement

### Purpose
Expand existing loyalty features into a comprehensive retention program.

### Current Features (Implemented)
- Points earning on purchases
- Tier levels (Bronze, Silver, Gold, Platinum)
- Points redemption

### Enhanced Features

#### 1. Points Earning Rules
| Action | Points | Notes |
|--------|--------|-------|
| Purchase | $1 = 1 point | Base earning |
| First Purchase | +50 bonus | Welcome bonus |
| Birthday Purchase | 2x points | Birthday month |
| Review Submission | +25 points | Encourage reviews |
| Referral (Referee) | +100 points | When friend orders |
| Social Share | +10 points | Once per day |
| Account Anniversary | +50 points | Yearly bonus |
| Tier Bonus | Up to 2x multiplier | Based on tier |

#### 2. Gamification Elements
- Progress bar to next tier
- Achievements/badges
- Limited-time challenges
- Streak bonuses (consecutive months purchasing)
- Surprise & delight random rewards

#### 3. Referral Program
```
┌─────────────────────────────────────────────────────────────┐
│ Your Referral Link:                                         │
│ https://pecosrivertraders.com/ref/JOHNSMITH123             │
│                                              [Copy] [Share] │
├─────────────────────────────────────────────────────────────┤
│ Your Referrals: 5 friends joined                           │
│ Earned: $50 in credits                                      │
│                                                             │
│ Give $10, Get $10 - Share with friends!                    │
└─────────────────────────────────────────────────────────────┘
```

#### 4. VIP Perks by Tier
| Tier | Spend Requirement | Perks |
|------|-------------------|-------|
| Bronze | $0+ | 1x points, Birthday bonus |
| Silver | $250+ | 1.25x points, Free shipping over $50 |
| Gold | $500+ | 1.5x points, Free shipping, Early sale access |
| Platinum | $1000+ | 2x points, Free shipping, Exclusive products, Dedicated support |

### Database Schema Addition
```sql
CREATE TABLE referrals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    referrer_id INT NOT NULL,
    referee_email VARCHAR(255) NOT NULL,
    referee_id INT,
    referral_code VARCHAR(20) NOT NULL,
    status ENUM('pending', 'signed_up', 'first_purchase', 'credited') DEFAULT 'pending',
    referrer_credit DECIMAL(10,2) DEFAULT 0,
    referee_credit DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    converted_at TIMESTAMP NULL,
    FOREIGN KEY (referrer_id) REFERENCES users(id),
    FOREIGN KEY (referee_id) REFERENCES users(id)
);

CREATE TABLE loyalty_achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    badge_icon VARCHAR(255),
    criteria JSON,
    points_reward INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE customer_achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    achievement_id INT NOT NULL,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_achievement (customer_id, achievement_id),
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (achievement_id) REFERENCES loyalty_achievements(id)
);
```

---

## Task & Activity Management

### Purpose
Track follow-ups, assignments, and team activities.

### Features

#### 1. Task Management
```
┌─────────────────────────────────────────────────────────────┐
│ My Tasks                               [+ Add Task] [Filter]│
├─────────────────────────────────────────────────────────────┤
│ □ Call John Smith re: wholesale inquiry      Due: Today    │
│ □ Follow up on order #1234 complaint         Due: Today    │
│ □ Send quote to ABC Corporation              Due: Tomorrow │
│ □ Review VIP customer list for Q4 campaign   Due: Nov 30   │
└─────────────────────────────────────────────────────────────┘
```

#### 2. Task Types
- **Call**: Phone follow-up
- **Email**: Send communication
- **Follow-up**: General follow-up
- **Review**: Review something
- **Meeting**: Scheduled meeting
- **Other**: Custom task

#### 3. Task Features
- Due date and time
- Priority level
- Assign to team member
- Link to customer/order/lead
- Recurring tasks
- Reminders/notifications
- Task completion tracking

#### 4. Activity Log
All staff activities logged:
- Customer profile views
- Order modifications
- Emails sent
- Notes added
- Settings changed

### Database Schema Addition
```sql
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    task_type ENUM('call', 'email', 'follow_up', 'review', 'meeting', 'other'),
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    due_date DATETIME,
    reminder_at DATETIME,
    assigned_to INT,
    assigned_by INT,
    related_type ENUM('customer', 'order', 'lead', 'ticket'),
    related_id INT,
    is_recurring BOOLEAN DEFAULT FALSE,
    recurrence_rule VARCHAR(100),
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES admin_users(id),
    FOREIGN KEY (assigned_by) REFERENCES admin_users(id)
);

CREATE TABLE activity_log (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created (created_at)
);
```

---

## Integration Hub

### Purpose
Connect with external CRM and marketing platforms.

### Features

#### 1. Native Integrations
| Platform | Type | Features |
|----------|------|----------|
| **Mailchimp** | Email Marketing | Sync customers, segments, purchase data |
| **Klaviyo** | Email/SMS Marketing | Advanced e-commerce flows |
| **HubSpot** | CRM | Two-way contact sync |
| **Salesforce** | CRM | Enterprise integration |
| **Zendesk** | Support | Ticket sync |
| **Intercom** | Chat/Support | Customer data enrichment |
| **Google Analytics** | Analytics | Enhanced e-commerce |
| **Facebook** | Advertising | Custom audiences |
| **QuickBooks** | Accounting | Customer/invoice sync |

#### 2. Data Export
- One-click export to CSV
- Scheduled exports (daily, weekly)
- Custom field selection
- GDPR-compliant data export

#### 3. API Access
- REST API for customer data
- Webhooks for real-time events
- Rate limiting and authentication
- API usage dashboard

#### 4. Webhook Events
| Event | Payload |
|-------|---------|
| `customer.created` | New customer data |
| `customer.updated` | Changed fields |
| `order.placed` | Order details |
| `order.shipped` | Tracking info |
| `loyalty.points_earned` | Points transaction |
| `loyalty.tier_changed` | New tier info |
| `support.ticket_created` | Ticket details |

### Database Schema Addition
```sql
CREATE TABLE integrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    provider VARCHAR(50) NOT NULL,
    config JSON,
    is_active BOOLEAN DEFAULT FALSE,
    last_sync TIMESTAMP,
    sync_status ENUM('idle', 'syncing', 'error'),
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE webhooks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(500) NOT NULL,
    events JSON NOT NULL,
    secret VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    last_triggered TIMESTAMP,
    failure_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## Implementation Priority

### Phase 1: Foundation (High Priority) - COMPLETED
Essential CRM features implemented:

1. **Customer 360 View Enhancement** - DONE
   - Activity timeline with icons and chronological display
   - Customer tags with add/remove functionality
   - Key metrics display (LTV, AOV, order count, health score)
   - RFM scoring and segment assignment
   - Customer health gauge visualization
   - Churn risk indicator
   - Pinned notes section

2. **Communication Log** - DONE
   - Note-taking system with pinning
   - Communication templates (5 default templates seeded)
   - Activity logging for all customer interactions
   - Email template management page

3. **Basic Segmentation** - DONE
   - Pre-built segments (8 preset segments seeded)
   - Simple segment builder with rule-based criteria
   - Customer Tags management page
   - Segments management page
   - Export functionality
   - 12 default customer tags seeded

**Phase 1 Completed**: November 29, 2025

### Phase 2: Engagement (Medium Priority) - COMPLETED
Features to increase customer engagement:

4. **Support Ticket System** - DONE
   - Basic ticketing with ticket numbers
   - Status tracking (Open, In Progress, Pending, Resolved, Closed)
   - Priority levels (Low, Medium, High, Urgent)
   - Category-based organization
   - Threaded conversation view
   - Canned responses management (8 default responses seeded)
   - Customer ticket history
   - Internal notes support
   - Quick status/priority updates
   - Satisfaction rating display
   - Admin pages: /admin/support, /admin/support/{id}, /admin/support-responses

5. **Marketing Automation Basics** - DONE
   - Database tables: automation_workflows, automation_steps, automation_enrollments, automation_logs, abandoned_carts
   - 5 preset workflow templates seeded (Abandoned Cart, Welcome Series, Post-Purchase, Win-Back, Birthday)
   - Task management table: admin_tasks

6. **Enhanced Loyalty** - DONE
   - Referrals table for referral program
   - Loyalty achievements table (10 achievements seeded)
   - Customer achievements tracking
   - Loyalty point rules table (7 rules seeded)

**Phase 2 Completed**: November 29, 2025

### Phase 3: Advanced (Lower Priority) - COMPLETED
Advanced features for mature CRM:

7. **Sales Pipeline** - DONE
   - Lead management with full CRUD operations
   - Lead sources configuration (8 sources seeded: Website, Trade Show, Referral, Cold Call, Social Media, Email Campaign, Partner, Advertisement)
   - Lead status tracking (New, Contacted, Qualified, Proposal, Negotiation, Won, Lost, Dormant)
   - Lead scoring system
   - Lead activities logging (Calls, Emails, Meetings, Notes)
   - Lead to Deal conversion workflow
   - Deal stages (6 stages seeded: Prospect, Qualified, Proposal, Negotiation, Closed Won, Closed Lost)
   - Deal pipeline view with Kanban-style board
   - Deal value tracking and probability
   - Deal activities logging
   - Wholesale accounts management
   - Wholesale tiers (Bronze, Silver, Gold, Platinum) with discount percentages
   - Wholesale orders tracking
   - Admin pages: /admin/leads, /admin/leads/{id}, /admin/deals, /admin/deals/{id}, /admin/wholesale, /admin/wholesale/{id}

8. **Advanced Analytics** - DONE
   - Custom reports table with configurable metrics, dimensions, and filters
   - Report snapshots for historical data
   - Customer cohorts for cohort analysis
   - Predictive scores table (churn probability, purchase prediction, LTV prediction, engagement score)
   - Chart type configuration (bar, line, pie, table)
   - Report scheduling support

9. **Integration Hub** - DONE
   - API keys management with scopes and rate limiting
   - Webhooks configuration with event subscriptions
   - Webhook logs for delivery tracking
   - External integrations table (Mailchimp, Klaviyo, QuickBooks, etc.)
   - Integration sync logs
   - Data exports table with format options (CSV, XLSX, JSON)

10. **Advanced Automation** - Database Ready
    - Workflow foundation from Phase 2
    - Ready for visual workflow builder implementation

**Phase 3 Completed**: November 29, 2025

---

## Product Management Enhancements

### Multiple Product Images - COMPLETED

Full support for multiple product images with the following features:

#### Backend API (ProductController)
- **Upload multiple images** - Up to 10 images per product (POST `/products/{upc}/images`)
- **Reorder images** - Drag-and-drop ordering saved via API (PUT `/products/{upc}/images/reorder`)
- **Set primary image** - Mark any image as primary (PUT `/products/{upc}/images/{id}/primary`)
- **Delete images** - Remove individual images (DELETE `/products/{upc}/images/{id}`)
- **Database table**: `product_images` with `display_order`, `is_primary`, `alt_text` fields

#### Frontend Admin Panel
- **Edit Modal**: Full image gallery with drag-drop reorder, set primary, delete
- **View Modal**: Image gallery with main image and thumbnail strip navigation
- **Product List**: Shows primary image with "+N" badge for multiple images

#### Image Storage
- New images stored in `/storage/products/` directory
- Legacy images supported from `/prt2/assets/` path
- Formats: JPEG, PNG, GIF, WebP (max 2MB each)

**Completed**: November 29, 2025

---

## Gap Analysis & Remaining Work

### Fully Implemented (Backend + Frontend)
| Feature | Status |
|---------|--------|
| Customer 360 View | ✅ Complete |
| Customer Tags | ✅ Complete |
| Customer Segments | ✅ Complete |
| Customer Notes | ✅ Complete |
| Activity Timeline | ✅ Complete |
| Communication Log | ✅ Complete |
| Email Templates | ✅ Complete |
| Support Tickets | ✅ Complete |
| Leads Management | ✅ Complete |
| Deals Pipeline | ✅ Complete |
| Product Multiple Images | ✅ Complete |
| Product History | ✅ Complete |

### Backend Ready (Needs Frontend UI)
| Feature | Backend | Frontend |
|---------|---------|----------|
| Marketing Automation Workflows | ✅ Tables + Seeds | ❌ Visual workflow builder needed |
| Wholesale Accounts | ✅ Tables + API | ⚠️ Needs verification |
| Advanced Analytics/Reports | ✅ Tables | ❌ Report builder UI needed |
| Integration Hub | ✅ API Keys + Webhooks tables | ❌ Settings UI needed |

---

## Summary

Implementing these CRM features will transform the Pecos River Traders admin panel from a basic order management system into a comprehensive customer relationship platform capable of:

- **Knowing customers deeply** through 360-degree profiles
- **Engaging intelligently** with automated, personalized communications
- **Retaining effectively** with loyalty programs and proactive service
- **Deciding with data** using advanced analytics and insights
- **Scaling efficiently** through automation and integrations

### Estimated Total Effort
- **Phase 1**: 5-7 weeks
- **Phase 2**: 7-10 weeks
- **Phase 3**: 13-19 weeks
- **Total**: 25-36 weeks (6-9 months for full implementation)

### Quick Wins (Can implement in days)
1. Customer notes field
2. Basic tagging
3. Email logging
4. Pre-built customer segments
5. Activity timeline (orders, reviews)

---

**Document Version**: 1.4
**Last Updated**: November 29, 2025
**All Phases Completed**: November 29, 2025
**Gap Analysis Added**: November 29, 2025
