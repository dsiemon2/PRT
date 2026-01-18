# Live Chat Support - Implementation Guide

Comprehensive guide for implementing live chat support for Pecos River Trading Company, covering both custom-built solutions and third-party platforms.

**Last Updated**: November 18, 2025

---

## Table of Contents

1. [Overview](#overview)
2. [Option 1: Build From Scratch](#option-1-build-from-scratch)
3. [Option 2: Third-Party Solutions](#option-2-third-party-solutions)
4. [Comparison Matrix](#comparison-matrix)
5. [Recommendation](#recommendation)
6. [Implementation Roadmap](#implementation-roadmap)

---

## Overview

Live chat support enables real-time customer assistance on the website, improving customer satisfaction, reducing support costs, and increasing conversion rates.

### Business Benefits

- **Increased Conversion**: 38% higher conversion rate for sites with live chat
- **Customer Satisfaction**: 73% of customers prefer live chat over phone/email
- **Cost Efficiency**: Handle 3-6 chats simultaneously vs 1 phone call at a time
- **Reduced Cart Abandonment**: Answer questions before customers leave
- **Valuable Insights**: Understand customer pain points in real-time
- **Competitive Advantage**: Stand out from competitors without live support

### Key Requirements for PRT

- Real-time messaging between customers and support agents
- Offline message collection when agents unavailable
- Mobile-responsive chat widget
- Visitor information (page viewing, cart contents, order history)
- Proactive chat triggers (e.g., "Need help finding something?")
- Chat history and transcripts
- Typing indicators and read receipts
- File/image sharing (for product questions)
- Integration with existing customer accounts
- Performance metrics and analytics

---

## Option 1: Build From Scratch

Build a custom live chat system using WebSockets for real-time communication.

### Architecture Overview

**Frontend**:
- Chat widget (floating button + chat window)
- Customer interface with message input, file upload
- Real-time message updates via WebSocket

**Backend**:
- WebSocket server (Node.js + Socket.io or PHP + Ratchet)
- Message storage (MySQL database)
- Agent dashboard for managing conversations
- Presence system (online/offline status)

**Database Tables**:
- `chat_conversations` - Conversation metadata
- `chat_messages` - Individual messages
- `chat_agents` - Support agent accounts
- `chat_settings` - Configuration and triggers

### Technology Stack Options

#### Option A: PHP + Ratchet WebSocket

**Stack**:
- **WebSocket Server**: Ratchet (PHP WebSocket library)
- **Frontend**: JavaScript (vanilla or jQuery)
- **Database**: MySQL (existing)
- **Queue**: Redis (for scaling)

**Pros**:
- ✅ Same language as existing codebase (PHP)
- ✅ Can reuse existing database connection and user system
- ✅ Team already knows PHP
- ✅ No additional server infrastructure initially

**Cons**:
- ❌ PHP not ideal for long-running WebSocket processes
- ❌ Requires dedicated process/port for WebSocket server
- ❌ Memory management concerns for long-running processes
- ❌ Less mature WebSocket ecosystem than Node.js

**Example Code**:
```php
// WebSocket server (bin/chat-server.php)
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Chat;

require dirname(__DIR__) . '/vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);

$server->run();
```

#### Option B: Node.js + Socket.io (RECOMMENDED)

**Stack**:
- **WebSocket Server**: Socket.io (Node.js)
- **Frontend**: JavaScript (vanilla or React)
- **Database**: MySQL (via mysql2 package)
- **Session**: Redis for shared sessions with PHP

**Pros**:
- ✅ **Best for WebSockets**: Node.js designed for real-time apps
- ✅ **Battle-tested**: Socket.io is industry standard
- ✅ **Excellent Performance**: Non-blocking I/O handles thousands of connections
- ✅ **Auto Reconnection**: Socket.io handles connection drops gracefully
- ✅ **Fallback Support**: Automatically falls back to polling if WebSocket unavailable
- ✅ **Rich Ecosystem**: Tons of packages for chat features
- ✅ **Scalability**: Easy to scale with Redis adapter

**Cons**:
- ❌ **Different Language**: Team needs Node.js knowledge
- ❌ **Separate Server**: Requires Node.js server running alongside Apache
- ❌ **Integration Complexity**: Need to share session data between PHP and Node.js

**Example Code**:
```javascript
// Server (server.js)
const io = require('socket.io')(3000, {
  cors: { origin: "http://localhost:8300" }
});

const mysql = require('mysql2/promise');
const db = mysql.createPool({
  host: 'localhost',
  user: 'root',
  password: '',
  database: 'prt_database'
});

io.on('connection', (socket) => {
  console.log('User connected:', socket.id);

  socket.on('join-chat', async (userId) => {
    socket.join(`user-${userId}`);
    // Load chat history from MySQL
    const [messages] = await db.query(
      'SELECT * FROM chat_messages WHERE user_id = ? ORDER BY created_at DESC LIMIT 50',
      [userId]
    );
    socket.emit('chat-history', messages);
  });

  socket.on('send-message', async (data) => {
    // Save message to database
    await db.query(
      'INSERT INTO chat_messages (conversation_id, sender_id, message, created_at) VALUES (?, ?, ?, NOW())',
      [data.conversationId, data.senderId, data.message]
    );

    // Broadcast to agent
    io.to(`conversation-${data.conversationId}`).emit('new-message', data);
  });

  socket.on('disconnect', () => {
    console.log('User disconnected:', socket.id);
  });
});

console.log('Chat server running on port 3000');
```

```javascript
// Client (chat-widget.js)
const socket = io('http://localhost:8300');

socket.on('connect', () => {
  console.log('Connected to chat server');
  socket.emit('join-chat', userId);
});

socket.on('chat-history', (messages) => {
  messages.forEach(msg => displayMessage(msg));
});

socket.on('new-message', (data) => {
  displayMessage(data);
});

function sendMessage(message) {
  socket.emit('send-message', {
    conversationId: currentConversation,
    senderId: userId,
    message: message
  });
}
```

### Database Schema

```sql
-- Conversations table
CREATE TABLE chat_conversations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    agent_id INT NULL,
    status ENUM('open', 'assigned', 'closed') DEFAULT 'open',
    priority ENUM('low', 'normal', 'high') DEFAULT 'normal',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    closed_at DATETIME NULL,
    FOREIGN KEY (customer_id) REFERENCES users(id),
    INDEX idx_status (status),
    INDEX idx_agent (agent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Messages table
CREATE TABLE chat_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    conversation_id INT NOT NULL,
    sender_id INT NOT NULL,
    sender_type ENUM('customer', 'agent', 'system') NOT NULL,
    message TEXT NOT NULL,
    attachment_url VARCHAR(255) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id),
    INDEX idx_conversation (conversation_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Agents table
CREATE TABLE chat_agents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    status ENUM('online', 'away', 'offline') DEFAULT 'offline',
    max_conversations INT DEFAULT 5,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Offline messages (when no agents available)
CREATE TABLE chat_offline_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    page_url VARCHAR(500),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_responded BOOLEAN DEFAULT FALSE,
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Chat triggers (proactive chat)
CREATE TABLE chat_triggers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    trigger_type ENUM('time_on_page', 'exit_intent', 'page_visit', 'cart_value') NOT NULL,
    trigger_value VARCHAR(100),
    page_url_pattern VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Frontend Implementation

**Chat Widget (HTML/CSS/JS)**:

```html
<!-- Chat Widget Button -->
<div id="chat-widget" class="chat-widget">
    <button id="chat-toggle" class="chat-toggle-btn">
        <i class="bi bi-chat-dots"></i>
        <span class="unread-badge" style="display:none;">0</span>
    </button>

    <div id="chat-window" class="chat-window" style="display:none;">
        <!-- Header -->
        <div class="chat-header">
            <div class="agent-info">
                <img src="/assets/images/agent-avatar.png" class="agent-avatar" alt="Agent">
                <div>
                    <strong>Support Team</strong>
                    <span class="status-indicator online"></span>
                </div>
            </div>
            <button id="chat-minimize" class="chat-minimize">
                <i class="bi bi-dash"></i>
            </button>
        </div>

        <!-- Messages Area -->
        <div class="chat-messages" id="chat-messages">
            <!-- Messages will be inserted here -->
        </div>

        <!-- Typing Indicator -->
        <div class="typing-indicator" style="display:none;">
            <span></span><span></span><span></span>
        </div>

        <!-- Input Area -->
        <div class="chat-input-area">
            <input type="text" id="chat-input" placeholder="Type a message..." class="form-control">
            <button id="chat-send" class="btn btn-primary">
                <i class="bi bi-send"></i>
            </button>
        </div>
    </div>
</div>

<style>
.chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
}

.chat-toggle-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--prt-red);
    color: white;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    font-size: 24px;
    cursor: pointer;
    transition: transform 0.2s;
    position: relative;
}

.chat-toggle-btn:hover {
    transform: scale(1.1);
}

.unread-badge {
    position: absolute;
    top: 0;
    right: 0;
    background: #ff0000;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 40px rgba(0,0,0,0.3);
    display: flex;
    flex-direction: column;
}

.chat-header {
    background: var(--prt-red);
    color: white;
    padding: 15px;
    border-radius: 10px 10px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.agent-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.agent-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid white;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-left: 5px;
}

.status-indicator.online { background: #00ff00; }
.status-indicator.away { background: #ffaa00; }
.status-indicator.offline { background: #999; }

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    background: #f5f5f5;
}

.message {
    margin-bottom: 15px;
    display: flex;
}

.message.customer {
    justify-content: flex-end;
}

.message-bubble {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 15px;
    word-wrap: break-word;
}

.message.customer .message-bubble {
    background: var(--prt-red);
    color: white;
    border-bottom-right-radius: 5px;
}

.message.agent .message-bubble {
    background: white;
    color: #333;
    border-bottom-left-radius: 5px;
}

.message-time {
    font-size: 11px;
    color: #999;
    margin-top: 5px;
}

.typing-indicator {
    padding: 10px 15px;
    background: white;
    border-radius: 15px;
    display: inline-block;
    margin-left: 15px;
}

.typing-indicator span {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #999;
    margin: 0 2px;
    animation: typing 1.4s infinite;
}

.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-10px); }
}

.chat-input-area {
    display: flex;
    padding: 15px;
    background: white;
    border-radius: 0 0 10px 10px;
    border-top: 1px solid #ddd;
}

.chat-input-area input {
    flex: 1;
    margin-right: 10px;
}
</style>

<script>
// Chat Widget JavaScript
const chatWidget = {
    socket: null,
    conversationId: null,
    userId: <?php echo $_SESSION['user_id'] ?? 'null'; ?>,

    init() {
        this.connectSocket();
        this.bindEvents();
    },

    connectSocket() {
        this.socket = io('http://localhost:8300');

        this.socket.on('connect', () => {
            console.log('Connected to chat');
            if (this.userId) {
                this.socket.emit('join-chat', this.userId);
            }
        });

        this.socket.on('chat-history', (messages) => {
            this.displayHistory(messages);
        });

        this.socket.on('new-message', (data) => {
            this.addMessage(data);
            if (data.sender_type === 'agent') {
                this.updateUnreadCount();
            }
        });

        this.socket.on('agent-typing', () => {
            document.querySelector('.typing-indicator').style.display = 'block';
        });

        this.socket.on('agent-stopped-typing', () => {
            document.querySelector('.typing-indicator').style.display = 'none';
        });
    },

    bindEvents() {
        document.getElementById('chat-toggle').addEventListener('click', () => {
            this.toggleChat();
        });

        document.getElementById('chat-minimize').addEventListener('click', () => {
            this.toggleChat();
        });

        document.getElementById('chat-send').addEventListener('click', () => {
            this.sendMessage();
        });

        document.getElementById('chat-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            }
        });
    },

    toggleChat() {
        const window = document.getElementById('chat-window');
        window.style.display = window.style.display === 'none' ? 'flex' : 'none';
        this.clearUnreadCount();
    },

    sendMessage() {
        const input = document.getElementById('chat-input');
        const message = input.value.trim();

        if (!message) return;

        this.socket.emit('send-message', {
            conversationId: this.conversationId,
            senderId: this.userId,
            message: message,
            senderType: 'customer'
        });

        input.value = '';
    },

    addMessage(data) {
        const messagesContainer = document.getElementById('chat-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${data.sender_type}`;

        messageDiv.innerHTML = `
            <div class="message-bubble">
                ${this.escapeHtml(data.message)}
                <div class="message-time">${this.formatTime(data.created_at)}</div>
            </div>
        `;

        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    },

    displayHistory(messages) {
        const container = document.getElementById('chat-messages');
        container.innerHTML = '';
        messages.reverse().forEach(msg => this.addMessage(msg));
    },

    updateUnreadCount() {
        const badge = document.querySelector('.unread-badge');
        const count = parseInt(badge.textContent) + 1;
        badge.textContent = count;
        badge.style.display = 'flex';
    },

    clearUnreadCount() {
        const badge = document.querySelector('.unread-badge');
        badge.textContent = '0';
        badge.style.display = 'none';
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    formatTime(datetime) {
        return new Date(datetime).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit'
        });
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    chatWidget.init();
});
</script>
```

### Agent Dashboard

Create `/admin/chat-dashboard.php` for support agents to manage conversations:

**Features**:
- List of active conversations
- Real-time message updates
- Assign conversations to agents
- View customer information and order history
- Canned responses/quick replies
- Mark conversations as resolved
- Search chat history

### Development Effort

**Timeline**: 6-8 weeks full-time development

**Phase 1 (2 weeks)**: Database schema, WebSocket server, basic chat widget
**Phase 2 (2 weeks)**: Agent dashboard, conversation management
**Phase 3 (2 weeks)**: Advanced features (file upload, typing indicators, proactive chat)
**Phase 4 (1 week)**: Testing, bug fixes
**Phase 5 (1 week)**: Deployment, documentation

**Estimated Cost**: $15,000 - $25,000 (contractor) or 320-400 developer hours

### Pros of Building From Scratch

- ✅ **Full Control**: Customize every aspect to exact needs
- ✅ **No Monthly Fees**: Only hosting costs after initial development
- ✅ **Data Ownership**: All chat data stays in your database
- ✅ **Integration**: Deep integration with existing user/order systems
- ✅ **Branding**: Complete control over UI/UX
- ✅ **No Limits**: Unlimited agents, conversations, features
- ✅ **Privacy**: No third-party access to customer data

### Cons of Building From Scratch

- ❌ **High Initial Cost**: $15K-$25K development
- ❌ **Long Timeline**: 6-8 weeks before launch
- ❌ **Maintenance**: Ongoing bug fixes and updates needed
- ❌ **Missing Features**: Must build everything yourself (mobile apps, analytics, etc.)
- ❌ **Infrastructure**: Need to manage WebSocket server
- ❌ **Scaling**: Must handle scaling challenges yourself
- ❌ **No Support**: No vendor support, rely on in-house team

---

## Option 2: Third-Party Solutions

Integrate an existing live chat platform via JavaScript snippet or API.

### Top Live Chat Platforms

---

### 1. **Intercom** ⭐ BEST OVERALL

**Overview**: Modern customer messaging platform with chat, automation, and CRM features.

**Pricing**:
- Starter: $74/month (2 seats)
- Pro: $395/month (5 seats)
- Premium: Custom pricing

**Pros**:
- ✅ **Beautiful UI**: Modern, clean interface
- ✅ **Automation**: Chatbots, auto-messages, triggers
- ✅ **Product Tours**: Onboard users with interactive guides
- ✅ **Help Center**: Built-in knowledge base
- ✅ **Email Integration**: Unified inbox for chat + email
- ✅ **Mobile Apps**: iOS/Android apps for agents
- ✅ **Rich Analytics**: Detailed conversation insights
- ✅ **CRM Features**: Customer profiles with context
- ✅ **API Access**: Extensive API for customization

**Cons**:
- ❌ **Expensive**: $74+/month ongoing cost
- ❌ **Overwhelming**: Many features you may not need
- ❌ **Per-Seat Pricing**: Costs add up with more agents

**Best For**: Companies wanting all-in-one customer communication platform

**Integration**:
```html
<script>
  window.intercomSettings = {
    api_base: "https://api-iam.intercom.io",
    app_id: "YOUR_APP_ID",
    name: "<?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>",
    email: "<?php echo $_SESSION['email']; ?>",
    created_at: <?php echo strtotime($_SESSION['created_at']); ?>
  };
</script>
<script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/YOUR_APP_ID';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(document.readyState==='complete'){l();}else if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();</script>
```

---

### 2. **Zendesk Chat** ⭐ BEST FOR ENTERPRISES

**Overview**: Enterprise-grade live chat as part of Zendesk support suite.

**Pricing**:
- Suite Team: $49/agent/month
- Suite Growth: $79/agent/month
- Suite Professional: $99/agent/month

**Pros**:
- ✅ **Enterprise Features**: Robust, reliable, scalable
- ✅ **Ticketing Integration**: Seamlessly converts chats to tickets
- ✅ **Advanced Routing**: Skills-based routing, omnichannel
- ✅ **Analytics**: Comprehensive reporting and dashboards
- ✅ **Integrations**: 1000+ app integrations
- ✅ **Security**: SOC 2, GDPR compliant
- ✅ **Mobile Apps**: Excellent mobile agent apps

**Cons**:
- ❌ **Expensive**: $49+/agent/month
- ❌ **Complex Setup**: Steep learning curve
- ❌ **Overkill**: Too much for small businesses

**Best For**: Large enterprises needing full support suite

**Integration**:
```html
<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=YOUR_KEY"> </script>
```

---

### 3. **Tidio** ⭐ BEST VALUE

**Overview**: Affordable live chat with chatbots, targeted by small businesses.

**Pricing**:
- Free: 50 conversations/month, 3 agents
- Starter: $29/month (100 conversations)
- Growth: $59/month (2,000 conversations)
- Plus: $749/month (unlimited)

**Pros**:
- ✅ **Affordable**: Best pricing for small businesses
- ✅ **Free Plan**: Generous free tier to start
- ✅ **Easy Setup**: 5-minute installation
- ✅ **Chatbots**: Visual chatbot builder included
- ✅ **Mobile Apps**: Chat with customers on the go
- ✅ **Email Integration**: Manage email + chat in one place
- ✅ **Pre-chat Surveys**: Collect info before chat starts
- ✅ **Visitor Tracking**: See what pages visitors are on

**Cons**:
- ❌ **Conversation Limits**: Pay per conversation on higher tiers
- ❌ **Basic Analytics**: Not as robust as Intercom/Zendesk
- ❌ **Limited Customization**: Less flexible branding

**Best For**: Small to medium businesses on a budget

**Integration**:
```html
<script src="//code.tidio.co/YOUR_PUBLIC_KEY.js" async></script>
```

---

### 4. **Drift** ⭐ BEST FOR B2B/SALES

**Overview**: Conversational marketing platform focused on sales and lead generation.

**Pricing**:
- Premium: $2,500/month
- Advanced: Custom pricing
- Enterprise: Custom pricing

**Pros**:
- ✅ **Sales Focus**: Built for qualifying and converting leads
- ✅ **Meeting Booking**: Schedule meetings directly from chat
- ✅ **Playbooks**: Automated conversation flows
- ✅ **ABM Features**: Account-based marketing tools
- ✅ **Video Chat**: One-click video calls
- ✅ **Salesforce Integration**: Deep CRM integration

**Cons**:
- ❌ **Very Expensive**: Starting at $2,500/month
- ❌ **Sales-Centric**: Not ideal for support-focused chat
- ❌ **Overkill**: Too much for e-commerce support

**Best For**: B2B companies with high-value deals

---

### 5. **LiveChat** ⭐ POPULAR CHOICE

**Overview**: Pure live chat platform with 20+ years in the market.

**Pricing**:
- Starter: $20/agent/month
- Team: $41/agent/month
- Business: $59/agent/month

**Pros**:
- ✅ **Reliable**: Proven platform with 20+ year track record
- ✅ **Simple**: Focused on chat, not bloated with features
- ✅ **Great UI**: Clean, intuitive interface
- ✅ **Chat Archives**: Unlimited chat history
- ✅ **File Sharing**: Send images/files in chat
- ✅ **Canned Responses**: Quick reply templates
- ✅ **Mobile Apps**: Good mobile agent experience
- ✅ **200+ Integrations**: Shopify, WordPress, Salesforce, etc.

**Cons**:
- ❌ **No Free Plan**: Must pay from day 1
- ❌ **Per-Agent Pricing**: Costs scale with team size
- ❌ **Basic Automation**: Limited chatbot features on lower tiers

**Best For**: Companies wanting straightforward, reliable live chat

**Integration**:
```html
<script>
    window.__lc = window.__lc || {};
    window.__lc.license = YOUR_LICENSE_NUMBER;
    ;(function(n,t,c){function i(n){return e._h?e._h.apply(null,n):e._q.push(n)}var e={_q:[],_h:null,_v:"2.0",on:function(){i(["on",c.call(arguments)])},once:function(){i(["once",c.call(arguments)])},off:function(){i(["off",c.call(arguments)])},get:function(){if(!e._h)throw new Error("[LiveChatWidget] You can't use getters before load.");return i(["get",c.call(arguments)])},call:function(){i(["call",c.call(arguments)])},init:function(){var n=t.createElement("script");n.async=!0,n.type="text/javascript",n.src="https://cdn.livechatinc.com/tracking.js",t.head.appendChild(n)}};!n.__lc.asyncInit&&e.init(),n.LiveChatWidget=n.LiveChatWidget||e}(window,document,[].slice))
</script>
```

---

### 6. **Crisp** ⭐ BEST FREE OPTION

**Overview**: Modern live chat with generous free tier.

**Pricing**:
- Basic: FREE (2 agents, unlimited conversations)
- Pro: $25/workspace/month (4 agents)
- Unlimited: $95/workspace/month (unlimited agents)

**Pros**:
- ✅ **Free Forever**: Best free plan (not a trial)
- ✅ **Unlimited Chats**: No conversation limits on free
- ✅ **Modern UI**: Beautiful, clean interface
- ✅ **Chatbots**: Basic automation on free plan
- ✅ **CRM**: Built-in customer profiles
- ✅ **Shared Inbox**: Email, chat, social media in one
- ✅ **Status Pages**: Create status page for outages
- ✅ **Knowledge Base**: Help center included

**Cons**:
- ❌ **2-Agent Limit**: Free plan limited to 2 agents
- ❌ **Fewer Integrations**: Smaller ecosystem than competitors
- ❌ **Basic Analytics**: Limited reporting on free tier

**Best For**: Startups and small businesses wanting free forever plan

**Integration**:
```html
<script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="YOUR_WEBSITE_ID";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
```

---

### 7. **Olark** ⭐ SIMPLE & AFFORDABLE

**Overview**: Straightforward live chat focused on simplicity.

**Pricing**:
- Standard: $29/agent/month (annual)

**Pros**:
- ✅ **Simple Pricing**: One flat rate, no tiers
- ✅ **Easy to Use**: Minimal learning curve
- ✅ **Automation**: Chatbots and auto-responders
- ✅ **Transcripts**: Automatic email transcripts
- ✅ **Searchable History**: Full chat archive search
- ✅ **Team Management**: Organize agents by department
- ✅ **CRM Integrations**: Salesforce, HubSpot, etc.

**Cons**:
- ❌ **Basic Features**: Lacks advanced functionality
- ❌ **Dated UI**: Not as modern as Intercom/Crisp
- ❌ **Limited Mobile**: Mobile app could be better

**Best For**: Small teams wanting simple, no-frills chat

**Integration**:
```html
<script data-cfasync="false" type='text/javascript'>/*<![CDATA[*/window.olark||(function(c){var f=window,d=document,l=f.location.protocol=="https:"?"https:":"http:",z=c.name,r="load";var nt=function(){f[z]=function(){(a.s=a.s||[]).push(arguments)};var a=f[z]._={},q=c.methods.length;while(q--){(function(n){f[z][n]=function(){f[z]("call",n,arguments)}})(c.methods[q])}a.l=c.loader;a.i=nt;a.p={0:+new Date};a.P=function(u){a.p[u]=new Date-a.p[0]};function s(){a.P(r);f[z](r)}f.addEventListener?f.addEventListener(r,s,false):f.attachEvent("on"+r,s);var ld=function(){function p(hd){hd="head";return["<",hd,"></",hd,"><",i,' onl' + 'oad="var d=',g,";d.getElementsByTagName('head')[0].",j,"(d.",h,"('script')).",k,"='",l,"//",a.l,"'",'"',"></",i,">"].join("")}var i="body",m=d[i];if(!m){return setTimeout(ld,100)}a.P(1);var j="appendChild",h="createElement",k="src",n=d[h]("div"),v=n[j](d[h](z)),b=d[h]("iframe"),g="document",e="domain",o;n.style.display="none";m.insertBefore(n,m.firstChild).id=z;b.frameBorder="0";b.id=z+"-loader";if(/MSIE[ ]+6/.test(navigator.userAgent)){b.src="javascript:false"}b.allowTransparency="true";v[j](b);try{b.contentWindow[g].open()}catch(w){c[e]=d[e];o="javascript:var d="+g+".open();d.domain='"+d.domain+"';";b[k]=o+"void(0);"}try{var t=b.contentWindow[g];t.write(p());t.close()}catch(x){b[k]=o+'d.write("'+p().replace(/"/g,String.fromCharCode(92)+'"')+'");d.close();'}a.P(2)};ld()};nt()})({loader: "static.olark.com/jsclient/loader0.js",name:"olark",methods:["configure","extend","declare","identify"]});
/* custom configuration goes here (www.olark.com/documentation) */
olark.identify('YOUR_SITE_ID');/*]]>*/</script>
```

---

### 8. **HubSpot Chat**

**Overview**: Free live chat included with HubSpot CRM.

**Pricing**:
- FREE (included with HubSpot CRM)
- Paid features: Part of Marketing Hub ($45+/month)

**Pros**:
- ✅ **Free**: Completely free with HubSpot CRM
- ✅ **CRM Integration**: Deep integration with HubSpot
- ✅ **Lead Capture**: Automatic contact creation
- ✅ **Chatbots**: Build bots with visual editor
- ✅ **Meeting Scheduler**: Book meetings from chat
- ✅ **Email Integration**: Unified inbox

**Cons**:
- ❌ **Requires HubSpot**: Must use HubSpot ecosystem
- ❌ **Limited Standalone**: Best when using full HubSpot
- ❌ **Basic Features**: Less robust than dedicated chat tools

**Best For**: Companies already using HubSpot CRM

**Integration**:
```html
<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/YOUR_HUB_ID.js"></script>
```

---

### 9. **Freshchat (Freshworks)**

**Overview**: Modern messaging platform by Freshworks.

**Pricing**:
- Free: 10 agents, 100 contacts
- Growth: $15/agent/month
- Pro: $39/agent/month
- Enterprise: $69/agent/month

**Pros**:
- ✅ **Good Free Tier**: 10 agents free
- ✅ **Omnichannel**: Chat, email, social, phone
- ✅ **AI Chatbots**: Freddy AI assistant
- ✅ **Mobile SDK**: Add chat to your mobile app
- ✅ **Campaign Messages**: Proactive outbound messaging
- ✅ **Marketplace**: 1000+ integrations

**Cons**:
- ❌ **Contact Limits**: Free plan limits to 100 contacts
- ❌ **Less Popular**: Smaller community than Zendesk/Intercom

**Best For**: Teams wanting free plan with many agents

**Integration**:
```html
<script>
  function initFreshChat() {
    window.fcWidget.init({
      token: "YOUR_TOKEN",
      host: "https://wchat.freshchat.com"
    });
  }
  function initialize(i,t){var e;i.getElementById(t)?initFreshChat():((e=i.createElement("script")).id=t,e.async=!0,e.src="https://wchat.freshchat.com/js/widget.js",e.onload=initFreshChat,i.head.appendChild(e))}function initiateCall(){initialize(document,"freshchat-js-sdk")}window.addEventListener?window.addEventListener("load",initiateCall,!1):window.attachEvent("load",initiateCall,!1);
</script>
```

---

### 10. **Tawk.to** ⭐ BEST 100% FREE

**Overview**: Completely free live chat forever (no catch).

**Pricing**:
- **100% FREE** (unlimited agents, unlimited chats)
- Optional: Hire agents from Tawk.to ($1/hour)

**Pros**:
- ✅ **Completely Free**: No hidden fees, no limits
- ✅ **Unlimited Everything**: Agents, chats, websites
- ✅ **No Branding**: Remove "Powered by" badge
- ✅ **Mobile Apps**: iOS/Android for agents
- ✅ **Monitoring**: Real-time visitor monitoring
- ✅ **Ticketing**: Built-in support tickets
- ✅ **Knowledge Base**: Create help center
- ✅ **Multilingual**: 45+ languages

**Cons**:
- ❌ **Less Polished**: UI not as modern as paid options
- ❌ **Limited Automation**: Basic chatbot features
- ❌ **Monetization Model**: Unclear long-term (could change)

**Best For**: Bootstrapped startups, nonprofits, anyone wanting free forever

**Integration**:
```html
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/YOUR_PROPERTY_ID/YOUR_WIDGET_ID';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
```

---

## Comparison Matrix

| Platform | Price (monthly) | Free Plan | Chatbots | Mobile App | Best For |
|----------|----------------|-----------|----------|------------|----------|
| **Intercom** | $74+ | 14-day trial | ✅ Advanced | ✅ Excellent | All-in-one platform |
| **Zendesk Chat** | $49+ | 14-day trial | ✅ Basic | ✅ Excellent | Enterprise support |
| **Tidio** | $0-$749 | ✅ 50 chats/mo | ✅ Good | ✅ Good | Small businesses |
| **Drift** | $2,500+ | 14-day trial | ✅ Advanced | ✅ Good | B2B sales teams |
| **LiveChat** | $20+ | 14-day trial | ✅ Basic | ✅ Excellent | Simple reliability |
| **Crisp** | $0-$95 | ✅ Unlimited | ✅ Basic | ✅ Good | Best free plan |
| **Olark** | $29 | 14-day trial | ✅ Basic | ✅ Average | Simple teams |
| **HubSpot** | FREE | ✅ Full CRM | ✅ Good | ✅ Good | HubSpot users |
| **Freshchat** | $0-$69 | ✅ 100 contacts | ✅ AI-powered | ✅ Good | Many free agents |
| **Tawk.to** | FREE | ✅ Unlimited | ⚠️ Limited | ✅ Good | Zero budget |

### Feature Comparison

| Feature | Build Custom | Intercom | Tidio | Crisp | Tawk.to |
|---------|-------------|----------|-------|-------|---------|
| **Setup Time** | 6-8 weeks | 1 hour | 30 min | 30 min | 30 min |
| **Initial Cost** | $15K-$25K | $0 | $0 | $0 | $0 |
| **Monthly Cost** | Hosting only | $74+ | $0-$749 | $0-$95 | $0 |
| **Customization** | ✅ Full | ⚠️ Limited | ⚠️ Limited | ⚠️ Limited | ⚠️ Limited |
| **Data Ownership** | ✅ Yes | ❌ No | ❌ No | ❌ No | ❌ No |
| **Chatbots** | Build yourself | ✅ Advanced | ✅ Good | ✅ Basic | ⚠️ Limited |
| **API Access** | ✅ Full control | ✅ Yes | ✅ Yes | ✅ Yes | ✅ Yes |
| **Analytics** | Build yourself | ✅ Excellent | ⚠️ Basic | ⚠️ Basic | ⚠️ Basic |
| **Support** | DIY | ✅ Excellent | ✅ Good | ✅ Good | ⚠️ Community |

---

## Recommendation

### For Pecos River Trading Company: **Use Third-Party Solution (Tidio or Crisp)**

**Why NOT Build From Scratch:**
1. **Time to Market**: Get live chat running this week, not in 2 months
2. **Proven Solution**: Battle-tested platforms handle edge cases you haven't thought of
3. **Mobile Apps**: Agents can respond from phone (building this is complex)
4. **Low Risk**: Try free, switch vendors if needed
5. **Feature Rich**: Chatbots, analytics, integrations included
6. **No Maintenance**: Vendor handles updates, scaling, security

**Recommended: Tidio (Starter Plan - $29/month)**

**Why Tidio:**
- ✅ **Free to start**: Test with 50 chats/month free
- ✅ **Affordable**: Only $29/month for 100 chats (likely enough for PRT)
- ✅ **Chatbots included**: Automate FAQs
- ✅ **Easy setup**: Add one script tag, done in 5 minutes
- ✅ **Visitor tracking**: See what products customers are viewing
- ✅ **Email integration**: Don't miss messages when offline
- ✅ **Mobile apps**: Answer chats on the go
- ✅ **Good support**: Responsive customer service

**Alternative: Crisp (Free Plan)**

If budget is extremely tight:
- ✅ **Free forever**: No cost, no limits
- ✅ **2 agents**: Likely enough for current team size
- ✅ **Modern UI**: Clean, professional look
- ✅ **Chatbots**: Basic automation included

### Implementation Plan

**Week 1: Trial & Setup**
1. Sign up for Tidio free trial
2. Install chat widget on staging site
3. Test chat functionality
4. Configure chatbot for common FAQs
5. Set up offline messages
6. Train 2 team members as agents

**Week 2: Customization & Launch**
1. Customize chat widget colors to match PRT branding
2. Write canned responses for common questions:
   - Order status
   - Shipping times
   - Product availability
   - Return policy
3. Set up proactive triggers:
   - "Can I help you find something?" after 30s on product page
   - "Need help with checkout?" on cart page
4. Launch on production site
5. Monitor first conversations

**Week 3: Optimization**
1. Analyze conversation topics
2. Add more FAQ chatbot flows
3. Adjust trigger timing based on data
4. Create more canned responses
5. Set business hours and offline message

**Ongoing:**
- Review chat transcripts weekly
- Update chatbot based on common questions
- Measure impact on conversion rate
- Consider upgrading if hitting conversation limits

---

## Build From Scratch: When It Makes Sense

**Consider custom development if:**
1. ✅ You need very specific integrations (e.g., ERP, custom inventory system)
2. ✅ Data privacy is critical (HIPAA, defense contracts)
3. ✅ You'll have 10+ chat agents (per-seat pricing becomes expensive)
4. ✅ You need to white-label the solution for clients
5. ✅ You have in-house dev team with capacity
6. ✅ You're building a SaaS product (chat is part of your product)

**Don't build from scratch if:**
1. ❌ You need chat operational quickly (< 3 months)
2. ❌ Limited technical team
3. ❌ Budget constrained (free/cheap options exist)
4. ❌ Small team (< 5 agents)
5. ❌ Don't want maintenance burden

---

## Implementation Code Snippets

### Tidio Implementation

```php
<!-- Add to includes/footer.php before closing </body> tag -->
<?php if (!isset($disableChat) || !$disableChat): ?>
<!-- Tidio Live Chat -->
<script src="//code.tidio.co/YOUR_PUBLIC_KEY_HERE.js" async></script>

<?php if (isset($_SESSION['user_id'])): ?>
<script>
// Pass user data to Tidio for better context
document.addEventListener('tidioChat-ready', function() {
    tidioChatApi.setVisitorData({
        distinct_id: "<?php echo $_SESSION['user_id']; ?>",
        email: "<?php echo $_SESSION['email'] ?? ''; ?>",
        name: "<?php echo ($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? ''); ?>",
        phone: "<?php echo $_SESSION['phone'] ?? ''; ?>",
        tags: ["customer"]
    });
});
</script>
<?php endif; ?>
<?php endif; ?>
```

### Crisp Implementation

```php
<!-- Add to includes/footer.php -->
<?php if (!isset($disableChat) || !$disableChat): ?>
<!-- Crisp Live Chat -->
<script type="text/javascript">
    window.$crisp=[];
    window.CRISP_WEBSITE_ID="YOUR_WEBSITE_ID_HERE";
    (function(){
        d=document;
        s=d.createElement("script");
        s.src="https://client.crisp.chat/l.js";
        s.async=1;
        d.getElementsByTagName("head")[0].appendChild(s);
    })();

    <?php if (isset($_SESSION['user_id'])): ?>
    // Set user data
    $crisp.push(["set", "user:email", ["<?php echo $_SESSION['email'] ?? ''; ?>"]]);
    $crisp.push(["set", "user:nickname", ["<?php echo ($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? ''); ?>"]]);
    $crisp.push(["set", "session:data", [[
        ["customer_id", "<?php echo $_SESSION['user_id']; ?>"],
        ["loyalty_tier", "<?php echo $_SESSION['loyalty_tier'] ?? 'Bronze'; ?>"]
    ]]]);
    <?php endif; ?>
</script>
<?php endif; ?>
```

### Disable Chat on Specific Pages

```php
// At top of checkout.php or any page where chat would be distracting
<?php
$disableChat = true; // This will prevent chat widget from loading
require_once(__DIR__ . '/../includes/header.php');
?>
```

---

## Performance Metrics to Track

Once live chat is implemented, track these KPIs:

### Customer Satisfaction
- **Chat Rating**: Average rating customers give after chat (aim for 4.5+/5)
- **Resolution Rate**: % of chats resolved without escalation (aim for 80%+)
- **Customer Feedback**: Qualitative comments about chat experience

### Response Times
- **First Response Time**: How long until agent responds (aim for < 2 min)
- **Average Handle Time**: Duration of typical chat (aim for 5-10 min)
- **Wait Time**: Time customer waits for agent (aim for < 1 min)

### Business Impact
- **Conversion Rate**: Do chat visitors convert more? (expect 20-40% lift)
- **Cart Abandonment**: Does chat reduce abandonment? (expect 10-15% reduction)
- **Order Value**: Do chat customers spend more? (often 10-15% higher AOV)
- **Support Ticket Reduction**: Chats that prevent email/phone support

### Operational Metrics
- **Chat Volume**: Conversations per day/week/month
- **Agent Utilization**: % of time agents are in active chats
- **Missed Chats**: Chats that went unanswered
- **Peak Hours**: When is chat busiest?
- **Common Topics**: What do customers ask about?

---

## Conclusion

**Bottom Line**: For Pecos River Trading Company, **implementing Tidio (starting free, upgrading to $29/month as needed) is the best path forward**.

You'll have professional live chat running within a day, can test it risk-free, and can always switch to another platform or build custom later if needs change.

Building from scratch makes sense for large enterprises with unique requirements, but for an e-commerce store, third-party solutions offer unbeatable time-to-value and cost-effectiveness.

---

**Next Steps:**
1. Sign up for Tidio free trial
2. Install widget on staging site
3. Test with team
4. Launch on production
5. Monitor for 30 days
6. Evaluate results and decide on upgrade

**Questions? Need Help?**
- Review this document
- Check vendor documentation
- Test on staging first
- Start simple, add features gradually

---

## RECOMMENDATION SUMMARY: Use Tawk.to (Free)

### Why Tawk.to for Live Agent Customer Support

**Yes, that clarifies it, but the recommendation remains: Use Tawk.to (free).**

Both Tawk.to and Crisp are **full live agent chat platforms**, not just FAQ bots. They're designed exactly for real humans chatting with customers in real-time.

### What You Get with Tawk.to for Live Agent Support:

**Live Agent Features:**
- ✅ **Real-time chat** - Agent types, customer sees it instantly
- ✅ **Multiple agents** - Unlimited team members can respond
- ✅ **Agent dashboard** - Desktop and mobile apps for agents to manage conversations
- ✅ **Visitor info** - See what page customer is on, their browsing history
- ✅ **Typing indicators** - "Agent is typing..."
- ✅ **File sharing** - Send/receive images, PDFs (useful for product questions)
- ✅ **Canned responses** - Quick replies for common questions
- ✅ **Chat assignment** - Route chats to specific agents/departments
- ✅ **Chat history** - Full transcript of all conversations
- ✅ **Offline messages** - Customers can leave messages when agents unavailable
- ✅ **Notifications** - Agents get alerts on desktop/mobile when new chat comes in

**Optional Bot Features** (you don't have to use):
- Chatbot can handle simple FAQs when agents are busy
- Or you can disable bots entirely and only do human-only chat

### Live Agent Chat Flow:

1. **Customer visits your site** → Sees chat widget
2. **Customer clicks chat** → "Hi, I have a question about shipping"
3. **Agent gets notification** → On desktop dashboard or mobile app
4. **Agent responds in real-time** → "Hi! I can help with that. Where are you located?"
5. **Conversation continues** → Back and forth until resolved
6. **Agent can see:**
   - Customer name (if logged in)
   - Current page they're viewing
   - Items in cart
   - Previous chat history
   - Order history (if you integrate it)

### Cost Comparison: Tawk.to vs Build Custom

| Feature | Tawk.to (Free) | Build Custom |
|---------|----------------|--------------|
| **Cost** | $0 | $15K-$25K |
| **Setup Time** | 30 minutes | 6-8 weeks |
| **Live Agent Chat** | ✅ Full featured | ✅ You'd build it |
| **Mobile Apps for Agents** | ✅ iOS + Android | ❌ Would cost $30K+ extra |
| **Typing Indicators** | ✅ Built-in | Build yourself |
| **File Sharing** | ✅ Built-in | Build yourself |
| **Offline Messages** | ✅ Built-in | Build yourself |
| **Chat Routing** | ✅ Built-in | Build yourself |
| **Desktop Notifications** | ✅ Built-in | Build yourself |
| **Maintenance** | ✅ Tawk.to handles | ❌ You maintain forever |

### When to Build Custom vs Use Tawk.to

**Building custom makes sense for:**
- ❌ Enterprise with 50+ agents
- ❌ SaaS companies where chat is part of the product
- ❌ Companies with unique compliance requirements (HIPAA, etc.)
- ❌ **NOT** for a bootstrapped e-commerce store needing live customer support

**Use Tawk.to (free) when:**
- ✅ You want professional live agent chat immediately for $0
- ✅ You need agents to respond from desktop or mobile apps
- ✅ You want no monthly fees draining budget pre-revenue
- ✅ You want to save $15K-$25K for inventory, marketing, revenue-generating activities
- ✅ You're a small to medium e-commerce business

### Final Decision:

**Use Tawk.to free. Start today.**

When you're making $100K+/month in revenue, then reconsider if you need custom (but you likely won't).

---

## Tawk.to Backend Implementation Guide

### Question: "If we implement Tawk.to in front end, what do we do to implement it in backend?"

**Short Answer: Almost nothing. Tawk.to handles 95% of backend for you.**

### What Tawk.to Provides (No Backend Work Needed):

1. **Message Storage** - Tawk.to stores all chat messages on their servers
2. **Agent Dashboard** - Fully functional web dashboard at dashboard.tawk.to
3. **Real-time Delivery** - WebSocket infrastructure handled by Tawk.to
4. **Notifications** - Email/push notifications to agents handled by Tawk.to
5. **User Management** - Agent accounts, permissions managed in Tawk.to dashboard
6. **Analytics** - Chat metrics, reports provided by Tawk.to
7. **Mobile Apps** - iOS/Android apps for agents provided by Tawk.to

### What YOU Do Implement (Optional Backend Enhancements):

#### 1. Pass Customer Data to Tawk.to (Optional but Recommended)

**Purpose**: Give agents context about who they're chatting with

**Frontend Implementation** (in footer.php):

```php
<!-- Tawk.to with Customer Context -->
<script type="text/javascript">
var Tawk_API = Tawk_API || {};

<?php if (isset($_SESSION['user_id'])): ?>
// Pass logged-in customer data to Tawk.to
Tawk_API.visitor = {
    name: '<?php echo htmlspecialchars(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '')); ?>',
    email: '<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>',
    hash: '<?php echo hash_hmac('sha256', $_SESSION['email'] ?? '', 'YOUR_TAWK_API_KEY'); ?>' // For security
};

// Custom attributes agents can see
Tawk_API.customAttributes = {
    'Customer ID': '<?php echo $_SESSION['user_id']; ?>',
    'Loyalty Tier': '<?php echo $_SESSION['loyalty_tier'] ?? 'Bronze'; ?>',
    'Total Orders': '<?php echo $_SESSION['total_orders'] ?? 0; ?>',
    'Account Created': '<?php echo date('Y-m-d', strtotime($_SESSION['created_at'] ?? 'now')); ?>'
};
<?php endif; ?>

var Tawk_LoadStart = new Date();
(function(){
    var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
    s1.async = true;
    s1.src = 'https://embed.tawk.to/YOUR_PROPERTY_ID/YOUR_WIDGET_ID';
    s1.charset = 'UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
})();
</script>
```

**What Agents See:**
- Customer name and email
- Customer ID (can look up in your admin)
- Loyalty tier
- Number of orders
- Account age

**No Backend Required**: This is all frontend JavaScript passing session data.

---

#### 2. Tawk.to Webhooks (Optional - For Advanced Integration)

**Purpose**: Get notified in your backend when chat events happen

**When to Use:**
- Save chat transcripts to your database
- Create support tickets from chat conversations
- Track chat interactions in customer profile
- Send follow-up emails after chat

**Backend Implementation** (create `/webhooks/tawk-webhook.php`):

```php
<?php
/**
 * Tawk.to Webhook Handler
 * Receives events from Tawk.to when chats happen
 */

require_once(__DIR__ . '/../config/database.php');

// Verify webhook is from Tawk.to
$tawkSignature = $_SERVER['HTTP_X_TAWK_SIGNATURE'] ?? '';
$payload = file_get_contents('php://input');
$expectedSignature = hash_hmac('sha256', $payload, 'YOUR_TAWK_WEBHOOK_SECRET');

if (!hash_equals($expectedSignature, $tawkSignature)) {
    http_response_code(401);
    exit('Unauthorized');
}

// Parse webhook data
$data = json_decode($payload, true);
$event = $data['event'] ?? '';

switch ($event) {
    case 'chat:start':
        // New chat started
        $visitorName = $data['visitor']['name'] ?? 'Guest';
        $visitorEmail = $data['visitor']['email'] ?? null;
        $chatId = $data['chatId'] ?? null;

        // Log to database
        $stmt = $dbConnect->prepare(
            "INSERT INTO chat_logs (chat_id, visitor_email, started_at) VALUES (?, ?, NOW())"
        );
        $stmt->execute([$chatId, $visitorEmail]);
        break;

    case 'chat:end':
        // Chat ended
        $chatId = $data['chatId'] ?? null;
        $transcript = $data['transcript'] ?? [];

        // Save transcript
        $stmt = $dbConnect->prepare(
            "UPDATE chat_logs SET ended_at = NOW(), transcript = ? WHERE chat_id = ?"
        );
        $stmt->execute([json_encode($transcript), $chatId]);

        // Optional: Send follow-up email
        // sendChatFollowupEmail($visitorEmail, $transcript);
        break;

    case 'ticket:create':
        // Chat converted to ticket
        $ticketId = $data['ticketId'] ?? null;
        // Handle ticket creation
        break;
}

http_response_code(200);
echo json_encode(['success' => true]);
?>
```

**Setup in Tawk.to Dashboard:**
1. Go to Administration → Webhooks
2. Add webhook URL: `https://yourdomain.com/webhooks/tawk-webhook.php`
3. Select events to receive (chat:start, chat:end, ticket:create, etc.)
4. Copy webhook secret for signature verification

**Database Schema** (optional, if storing chat logs):

```sql
CREATE TABLE chat_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    chat_id VARCHAR(100) UNIQUE,
    visitor_email VARCHAR(255),
    customer_id INT NULL,
    started_at DATETIME,
    ended_at DATETIME NULL,
    transcript JSON NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id),
    INDEX idx_chat_id (chat_id),
    INDEX idx_customer (customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

#### 3. Tawk.to API Integration (Optional - For Reading Chat Data)

**Purpose**: Pull chat history, visitor info, or agent stats into your admin panel

**Use Cases:**
- Display chat history on customer profile page in admin
- Show "Recent Chats" widget on admin dashboard
- Generate custom analytics reports

**Backend Implementation** (create `/includes/tawk-api.php`):

```php
<?php
/**
 * Tawk.to API Helper Functions
 */

class TawkAPI {
    private $apiKey;
    private $baseUrl = 'https://api.tawk.to/v3/';

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    /**
     * Get chat history for a specific customer email
     */
    public function getChatsByEmail($email) {
        $url = $this->baseUrl . 'chats?email=' . urlencode($email);
        return $this->makeRequest($url);
    }

    /**
     * Get specific chat transcript
     */
    public function getChatTranscript($chatId) {
        $url = $this->baseUrl . 'chats/' . $chatId;
        return $this->makeRequest($url);
    }

    /**
     * Get agent statistics
     */
    public function getAgentStats($agentId, $startDate, $endDate) {
        $url = $this->baseUrl . "agents/$agentId/stats?from=$startDate&to=$endDate";
        return $this->makeRequest($url);
    }

    private function makeRequest($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return ['error' => 'API request failed', 'code' => $httpCode];
        }

        return json_decode($response, true);
    }
}

// Usage example:
// $tawk = new TawkAPI('YOUR_API_KEY');
// $chats = $tawk->getChatsByEmail('customer@example.com');
?>
```

**Display Chat History in Admin** (e.g., in customer profile):

```php
<?php
// In admin/customer-profile.php
require_once(__DIR__ . '/../includes/tawk-api.php');

$tawk = new TawkAPI('YOUR_API_KEY');
$customerEmail = $customer['email'];
$chats = $tawk->getChatsByEmail($customerEmail);
?>

<div class="card">
    <div class="card-header">
        <h5>Chat History</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($chats['data'])): ?>
            <div class="list-group">
                <?php foreach ($chats['data'] as $chat): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <strong><?php echo date('M j, Y g:i A', strtotime($chat['createdAt'])); ?></strong>
                            <span class="badge bg-<?php echo $chat['status'] === 'closed' ? 'secondary' : 'success'; ?>">
                                <?php echo ucfirst($chat['status']); ?>
                            </span>
                        </div>
                        <p class="mb-1"><?php echo htmlspecialchars($chat['subject'] ?? 'General Inquiry'); ?></p>
                        <small>Agent: <?php echo htmlspecialchars($chat['agent']['name'] ?? 'Unassigned'); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">No chat history found.</p>
        <?php endif; ?>
    </div>
</div>
```

---

#### 4. Hide Chat Widget on Specific Pages (Frontend Control)

**Purpose**: Don't show chat on checkout or other pages where it's distracting

**Implementation** (in page-specific PHP files):

```php
<?php
// At top of checkout.php, login.php, etc.
$hideTawkChat = true;
require_once(__DIR__ . '/../includes/header.php');
?>
```

**In footer.php**:

```php
<?php if (!isset($hideTawkChat) || !$hideTawkChat): ?>
<!-- Tawk.to Script -->
<script type="text/javascript">
    // ... Tawk.to script here ...
</script>
<?php endif; ?>
```

---

### Summary: What Backend Work is Required?

| Feature | Backend Work Required | Complexity | Worth It? |
|---------|----------------------|------------|-----------|
| **Basic Chat Widget** | ❌ None (just add script tag) | Very Easy | ✅ YES - Do this |
| **Pass Customer Data** | ❌ None (frontend JS only) | Easy | ✅ YES - Do this |
| **Webhooks** | ✅ Create webhook handler | Medium | ⚠️ Optional - Only if you need chat logs in DB |
| **API Integration** | ✅ API wrapper functions | Medium | ⚠️ Optional - Only if showing chat history in admin |
| **Hide on Pages** | ❌ None (simple PHP variable) | Very Easy | ✅ YES - Do this |

### Recommended Implementation (Minimal Backend):

**Week 1: Launch (15 minutes)**
1. Add Tawk.to script to footer.php ✅
2. Pass customer data via frontend JavaScript ✅
3. Hide chat on checkout/login pages ✅
4. Done - chat is live with zero backend work

**Future (If Needed):**
- Add webhooks if you want chat transcripts in your database
- Add API integration if you want chat history in admin panel

### The Key Point:

**Tawk.to IS the backend.** They handle:
- Message storage
- Agent dashboard
- Real-time communication
- Notifications
- Analytics
- Everything

**You just embed their widget (frontend) and optionally enhance with webhooks/API (backend).** But you can launch with zero backend work.

---

## How YOU (The Agent) Chat with Customers Using Tawk.to

### Question: "If I am a real life agent and want to chat with customers, how does Tawk.to handle that?"

**Short Answer: You use Tawk.to's agent dashboard (web, desktop app, or mobile app) to respond to customer chats in real-time.**

### Agent Workflow: Step-by-Step

#### **Step 1: Agent Setup (One-Time)**

1. **Sign up for Tawk.to** at https://www.tawk.to
2. **Add your property** (your website - pecosrivertraders.com)
3. **Install widget code** on your website (we covered this above)
4. **Add team members** as agents:
   - Go to Administration → Agents
   - Click "Add Agent"
   - Enter agent email, name, role
   - They receive invitation email
   - They create account and log in

**Agent Roles:**
- **Administrator**: Full access, can manage settings
- **Agent**: Can chat with customers, limited admin access
- **Monitor**: Can view chats but not respond

#### **Step 2: Agent Goes Online**

**Option A: Web Dashboard** (Easiest - No Install)
1. Agent goes to https://dashboard.tawk.to
2. Logs in with their credentials
3. Dashboard shows:
   - List of active visitors on website (real-time)
   - Current conversations
   - Waiting customers
   - Chat history
4. Agent status: **Online**, **Away**, or **Offline**

**Option B: Desktop App** (Recommended for Full-Time Agents)
1. Download from https://www.tawk.to/downloads/
2. Available for Windows, Mac, Linux
3. Install and log in
4. **Advantages over web:**
   - Desktop notifications even when browser closed
   - Always-on tray icon
   - Faster, more responsive
   - Can minimize to system tray

**Option C: Mobile App** (For On-the-Go Agents)
1. Download from App Store (iOS) or Google Play (Android)
2. Log in with credentials
3. **Advantages:**
   - Respond to chats from anywhere
   - Push notifications on phone
   - Perfect for small teams where agents aren't at desk 24/7

---

### Real-Time Chat Flow (Agent Side)

#### **When Customer Starts Chat:**

**Customer Side:**
1. Customer visits your website
2. Sees Tawk.to chat widget (floating button bottom-right)
3. Clicks widget
4. Types: "Hi, I have a question about your boots"

**Agent Side:**
1. 🔔 **Notification**: Desktop/mobile notification "New chat from visitor"
2. **Chat appears in queue** in agent dashboard
3. Agent sees:
   - Customer name (if logged in) or "Visitor"
   - Message preview: "Hi, I have a question about your boots"
   - Customer location (IP-based): "Austin, TX"
   - Current page: "Product: Work Boots - Brown Leather"
   - Browsing history: Pages they visited
   - Custom attributes: Customer ID, loyalty tier, order count (if you set this up)

#### **Agent Accepts Chat:**

**Agent Actions:**
1. Clicks on the chat to open conversation window
2. Sees full chat interface:
   ```
   ┌─────────────────────────────────────────┐
   │ Visitor (John Smith)                    │
   │ john.smith@email.com                    │
   │ Austin, TX - On: Product Page           │
   │ Loyalty Tier: Gold | Orders: 12         │
   ├─────────────────────────────────────────┤
   │                                         │
   │ [Customer] Hi, I have a question        │
   │            about your boots             │
   │            3:45 PM                       │
   │                                         │
   │ [Type your message here...            ] │
   │ [📎 Attach] [😊 Emoji] [💾 Canned]     │
   └─────────────────────────────────────────┘
   ```

3. Agent types response: "Hi John! I'd be happy to help. What would you like to know about our work boots?"
4. Presses Enter or clicks Send
5. **Customer sees response instantly** on their side

#### **Conversation Continues:**

**Real-Time Two-Way Chat:**
- Customer types → Agent sees it instantly (with typing indicator "John is typing...")
- Agent types → Customer sees it instantly (with "Agent is typing...")
- Back-and-forth conversation like text messaging
- Both can send:
  - Text messages
  - Emojis
  - Files/images (customer can send photo of boot for sizing help)
  - Links

**Agent Tools During Chat:**

1. **Canned Responses** (Quick Replies)
   - Pre-written answers to common questions
   - Click "💾 Canned" → Select response → Instantly sent
   - Examples:
     - "Our shipping takes 3-5 business days"
     - "Returns are accepted within 30 days"
     - "Let me check our inventory for you"

2. **Transfer Chat**
   - If agent can't help: Transfer to another agent
   - "This is about returns, let me connect you to Sarah"

3. **Tag Conversation**
   - Add tags: "Product Question", "Shipping", "Complaint"
   - Helps with analytics later

4. **Add Notes** (Internal Only)
   - Private notes other agents can see
   - "Customer wants size 10, checking warehouse"

5. **View Customer Info**
   - Order history
   - Previous chats
   - Cart contents (if integrated)

---

### Agent Dashboard Overview

**When logged into dashboard.tawk.to, agent sees:**

#### **Left Sidebar:**
```
┌──────────────────────┐
│ 🏠 Home              │
│ 💬 Conversations (3) │ ← Active chats
│ 📊 Monitoring        │ ← Live visitors
│ 📧 Tickets           │
│ 📈 Reports           │
│ ⚙️  Settings         │
└──────────────────────┘
```

#### **Main Area - Active Conversations:**
```
┌─────────────────────────────────────────────────┐
│ John Smith                    [Austin, TX] 3:47 PM │
│ "Do you have size 10 in stock?"                   │
│ Product Page: Work Boots                          │
├─────────────────────────────────────────────────┤
│ Sarah Johnson              [Denver, CO] 3:50 PM   │
│ "What's your return policy?"                      │
│ Returns & Exchanges Page                          │
├─────────────────────────────────────────────────┤
│ Guest Visitor                  [NYC, NY] 3:52 PM  │
│ "I need help with checkout"                       │
│ Shopping Cart Page                                │
└─────────────────────────────────────────────────┘
```

#### **Monitoring Tab - Live Visitors:**
Shows all people currently on your website:
```
┌─────────────────────────────────────────────────┐
│ 👤 Visitor from Dallas, TX                       │
│    Viewing: Homepage → Products → Boot Details   │
│    Duration: 3 min 45 sec                        │
│    [Start Chat]                                  │
├─────────────────────────────────────────────────┤
│ 👤 John Smith (Logged In)                        │
│    Viewing: Checkout Page                        │
│    Cart: 2 items ($189.99)                       │
│    [Start Chat]                                  │
└─────────────────────────────────────────────────┘
```

**Agent can proactively start chat:**
- Click "Start Chat" next to visitor
- Send message: "Hi! I see you're checking out. Need any help?"
- Visitor sees message pop up in widget

---

### How Multiple Agents Work

#### **Scenario: 2+ Agents Online**

**Chat Distribution:**
1. **Customer starts chat** → Goes to first available agent
2. **Round-robin** or **Load balancing**:
   - Agent 1 has 2 active chats
   - Agent 2 has 1 active chat
   - New chat goes to Agent 2
3. **Agent-specific routing** (optional):
   - Set up departments (Sales, Support, Returns)
   - Route chats based on customer selection

**Agent Collaboration:**
1. **Transfer chat**: Agent A can transfer to Agent B mid-conversation
2. **See each other's chats**: Supervisors can view all conversations
3. **Jump in**: If agent needs help, another can jump into same chat

---

### Offline Mode (No Agents Available)

#### **When All Agents Are Offline:**

**Customer Side:**
- Sees "Leave a message" instead of live chat
- Fills out form:
  - Name
  - Email
  - Message
- Submits

**Agent Side:**
- Receives **email notification** with message
- Message appears in dashboard as "Ticket"
- Agent can respond via:
  - Email (reply to notification)
  - Dashboard (converts to ticket, can email customer back)

**Auto-Response:**
- Customer sees: "Thanks for your message! We'll respond within 24 hours."

---

### Mobile Agent Experience

**Example: You're out but customer needs help**

1. **Customer starts chat** on website at 2:30 PM
2. **Your phone buzzes** 📱 - Push notification
   ```
   Tawk.to - New Chat
   Visitor: "Do you ship internationally?"
   Tap to respond
   ```
3. **You open Tawk.to app** on your phone
4. **See chat interface** (just like texting):
   ```
   ┌─────────────────────────────┐
   │ Visitor                     │
   │ Austin, TX                  │
   ├─────────────────────────────┤
   │                             │
   │ Visitor:                    │
   │ Do you ship internationally?│
   │ 2:30 PM                     │
   │                             │
   ├─────────────────────────────┤
   │ [Type message here...]      │
   │ [Attach] [Emoji] [Canned]   │
   └─────────────────────────────┘
   ```
5. **Tap canned response** or type: "Yes! We ship to Canada and Mexico. Shipping takes 7-10 business days."
6. **Customer sees instantly** on their end
7. **Conversation continues** right from your phone

---

### Setting Up Agents (Quick Guide)

#### **1. Create Tawk.to Account**
- Go to https://www.tawk.to
- Sign up (free)
- Create property (your website)

#### **2. Add Team Members**
- Dashboard → Administration → Agents
- Click "Add Agent"
- Enter:
  - Name: "Sarah Johnson"
  - Email: sarah@pecosrivertraders.com
  - Role: Agent
- Click Send Invite
- Sarah receives email, creates password, logs in

#### **3. Install Dashboard Apps**
- **Desktop**: https://www.tawk.to/downloads/
- **Mobile**: App Store / Google Play → Search "Tawk.to"
- Log in with agent credentials

#### **4. Set Working Hours**
- Dashboard → Administration → Working Hours
- Set business hours: Mon-Fri 9 AM - 5 PM
- Outside hours: Show offline message form

#### **5. Create Canned Responses**
- Dashboard → Settings → Canned Responses
- Add common answers:
  - Shipping policy
  - Return policy
  - Product availability
  - Business hours
- Agents can insert with one click

---

### Comparison: Agent Experience

| Feature | Tawk.to Agent Dashboard | Custom Built |
|---------|------------------------|--------------|
| **Access Method** | Web + Desktop + Mobile | Would need to build all |
| **Notifications** | Push, email, desktop | Build yourself |
| **Chat Interface** | Professional, tested | Design from scratch |
| **File Sharing** | Built-in | Build yourself |
| **Canned Responses** | Built-in | Build yourself |
| **Chat Transfer** | Built-in | Build yourself |
| **Visitor Info** | Built-in | Build yourself |
| **Mobile App** | iOS + Android (free) | $50K+ to build |
| **Multi-Agent** | Built-in routing | Build yourself |
| **Training Needed** | 15 minutes | Custom UI training |
| **Cost for Agents** | $0 (unlimited) | Build + maintain |

---

### Real-World Agent Workflow Example

**Monday morning at Pecos River Traders:**

**9:00 AM:**
- Sarah (Agent) opens Tawk.to desktop app
- Sets status to "Online"
- Dashboard shows 2 visitors browsing website

**9:15 AM:**
- 🔔 Notification: "New chat from John Smith"
- John asks: "Do you have cowboy boots in size 11?"
- Sarah checks inventory (in separate tab/system)
- Sarah replies: "Yes! We have 3 styles in size 11. Would you like me to send you links?"
- John: "Yes please!"
- Sarah sends product links in chat
- John clicks link, adds to cart
- Sarah: "Anything else I can help with?"
- John: "No, thanks!"
- Sarah ends chat, marks as "Resolved"

**9:30 AM:**
- 🔔 Another chat: Guest visitor on checkout page
- Guest: "I'm having trouble applying my coupon code"
- Sarah: "I can help! What's the code you're trying to use?"
- Guest: "SAVE20"
- Sarah: "That code expired last week. But I can give you WELCOME10 for 10% off"
- Saves the sale ✅

**2:00 PM:**
- Sarah goes to lunch, sets status to "Away"
- Tom (Agent 2) is online, takes over new chats
- Sarah still gets notifications but Tom handles them

**5:00 PM:**
- Both agents go offline
- Widget switches to "Leave a message" form
- Customer submits message at 7 PM
- Sarah receives email, responds next morning

---

### Summary: How Agents Use Tawk.to

**You don't build anything for agents. Tawk.to provides:**

✅ **Web dashboard** at dashboard.tawk.to
✅ **Desktop apps** for Windows/Mac/Linux
✅ **Mobile apps** for iOS/Android
✅ **Real-time chat interface** (like messaging apps)
✅ **Notifications** when customers chat
✅ **Visitor info** and browsing history
✅ **Canned responses** for quick replies
✅ **Chat routing** for multiple agents
✅ **Offline message handling**
✅ **Chat history and analytics**

**Your only job:**
1. Sign up for Tawk.to
2. Add team members as agents
3. They log into dashboard/apps
4. They respond to customers in real-time

**That's it. Zero backend work for agent functionality.**

---

**Document Version**: 1.2
**Last Updated**: November 18, 2025
**Author**: Development Team
