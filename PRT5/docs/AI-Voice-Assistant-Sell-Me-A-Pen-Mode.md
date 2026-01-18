# AI Digital Voice Assistant - "Sell Me A Pen" Mode Toggle

> **Document Purpose:** Detailed specification for the dual-mode "Sell Me A Pen" voice assistant feature, allowing users to either be sold to by AI or practice their sales pitch against AI.

> **Last Updated:** December 2025

---

## Table of Contents

1. [Concept Overview](#concept-overview)
2. [Mode Definitions](#mode-definitions)
3. [User Interface](#user-interface)
4. [Conversation Flows](#conversation-flows)
5. [AI Behavior Specifications](#ai-behavior-specifications)
6. [Technical Requirements](#technical-requirements)
7. [Scoring & Feedback (User Sells Mode)](#scoring--feedback-user-sells-mode)
8. [Use Cases](#use-cases)

---

## Concept Overview

The "Sell Me A Pen" challenge is a classic sales training exercise made famous by the movie "The Wolf of Wall Street." This feature transforms it into an interactive voice experience with two distinct modes:

| Mode | Who Sells | Who Buys | Purpose |
|------|-----------|----------|---------|
| **AI Sells** | AI Agent | Human User | Entertainment, product discovery, demo AI capabilities |
| **User Sells** | Human User | AI Agent | Sales training, practice, skill development |

### The Toggle

A simple switch allows users to flip between modes before starting a session:

```
┌─────────────────────────────────────────┐
│         SELL ME A PEN                   │
│                                         │
│   ○ AI Sells the Pen                    │
│   ● User Sells the Pen                  │
│                                         │
│         [Start Session]                 │
└─────────────────────────────────────────┘
```

---

## Mode Definitions

### Mode 1: AI Sells the Pen

**Description:** The AI takes on the role of a salesperson attempting to sell a pen (or other product) to the user.

**Flow:**
1. AI delivers greeting
2. AI waits for user to say "Sell me a pen" (or variation)
3. AI performs sales pitch
4. User can interact, object, negotiate
5. Session ends with sale or rejection

**Target Audience:**
- Curious visitors wanting to see AI in action
- Potential customers exploring products
- Entertainment seekers

---

### Mode 2: User Sells the Pen

**Description:** The user takes on the role of salesperson, and the AI becomes a potential customer with varying personalities and objection styles.

**Flow:**
1. AI delivers greeting
2. AI says "Sell me a pen"
3. User performs their sales pitch
4. AI responds as customer (with objections, questions, interest)
5. Session ends with AI "buying" or rejecting
6. AI provides feedback/score on user's performance

**Target Audience:**
- Sales professionals practicing pitches
- Students learning sales techniques
- Job candidates preparing for interviews
- Anyone wanting to improve persuasion skills

---

## User Interface

### Toggle Component

```html
<!-- Mode Selection Toggle -->
<div class="sell-mode-toggle">
  <h3>Choose Your Challenge</h3>

  <div class="mode-option" data-mode="ai-sells">
    <div class="mode-icon">🤖</div>
    <div class="mode-info">
      <h4>AI Sells the Pen</h4>
      <p>Watch the AI try to sell you a pen. Can it convince you?</p>
    </div>
    <input type="radio" name="sellMode" value="ai-sells">
  </div>

  <div class="mode-option" data-mode="user-sells">
    <div class="mode-icon">🎯</div>
    <div class="mode-info">
      <h4>You Sell the Pen</h4>
      <p>Practice your sales pitch. The AI will challenge you!</p>
    </div>
    <input type="radio" name="sellMode" value="user-sells">
  </div>
</div>
```

### Difficulty Selection (User Sells Mode Only)

When "User Sells" is selected, show additional options:

```
┌─────────────────────────────────────────┐
│   Select Customer Difficulty            │
│                                         │
│   ○ Easy - Friendly, few objections     │
│   ○ Medium - Some pushback, skeptical   │
│   ○ Hard - Tough customer, many excuses │
│   ○ Expert - "Wolf of Wall Street"      │
│                                         │
└─────────────────────────────────────────┘
```

---

## Conversation Flows

### Flow 1: AI Sells the Pen

```
┌─────────────────────────────────────────────────────────────┐
│                    AI SELLS MODE                             │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
              ┌─────────────────────────┐
              │   AI: Greeting          │
              │   "Hello! Welcome to    │
              │   Sell Me A Pen. I'm    │
              │   ready when you are."  │
              └─────────────────────────┘
                            │
                            ▼
              ┌─────────────────────────┐
              │   WAIT STATE            │
              │   Listening for:        │
              │   - "Sell me a pen"     │
              │   - "Go ahead"          │
              │   - "Let's see it"      │
              │   - "Show me"           │
              └─────────────────────────┘
                            │
                            ▼
              ┌─────────────────────────┐
              │   AI: Sales Pitch       │
              │   [Dynamic pitch based  │
              │    on product/persona]  │
              └─────────────────────────┘
                            │
                            ▼
              ┌─────────────────────────┐
              │   USER: Response        │
              │   - Questions           │
              │   - Objections          │
              │   - Interest            │
              │   - Rejection           │
              └─────────────────────────┘
                            │
                            ▼
              ┌─────────────────────────┐
              │   AI: Handle Response   │
              │   [Overcome objections, │
              │    answer questions,    │
              │    close the sale]      │
              └─────────────────────────┘
                            │
                            ▼
              ┌─────────────────────────┐
              │   OUTCOME               │
              │   - Sale Made ✓         │
              │   - Sale Lost ✗         │
              │   - User Ended Session  │
              └─────────────────────────┘
```

---

### Flow 2: User Sells the Pen

```
┌─────────────────────────────────────────────────────────────┐
│                   USER SELLS MODE                            │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
              ┌─────────────────────────┐
              │   AI: Greeting          │
              │   "Hello! I'm a busy    │
              │   [persona]. Let's see  │
              │   what you've got..."   │
              └─────────────────────────┘
                            │
                            ▼
              ┌─────────────────────────┐
              │   AI: Challenge         │
              │   "SELL ME A PEN."      │
              │                         │
              └─────────────────────────┘
                            │
                            ▼
              ┌─────────────────────────┐
              │   USER: Sales Pitch     │
              │   [User speaks their    │
              │    pitch attempt]       │
              └─────────────────────────┘
                            │
                            ▼
              ┌─────────────────────────┐
              │   AI: Customer Response │
              │   - Objection           │
              │   - Question            │
              │   - Interest shown      │
              │   - Dismissal           │
              └─────────────────────────┘
                            │
                            ▼
              ┌─────────────────────────┐
              │   [Conversation Loop]   │
              │   User responds to AI   │
              │   AI responds to User   │
              │   Until conclusion      │
              └─────────────────────────┘
                            │
                            ▼
              ┌─────────────────────────┐
              │   OUTCOME + FEEDBACK    │
              │   - Sale Made ✓ Score   │
              │   - Sale Lost ✗ Tips    │
              │   - Detailed Analysis   │
              └─────────────────────────┘
```

---

## AI Behavior Specifications

### Greeting Scripts

#### AI Sells Mode - Greeting
```
Variations (randomly selected):

1. "Hello! Welcome to Sell Me A Pen. Whenever you're ready,
    just say 'sell me a pen' and I'll show you what I've got."

2. "Hey there! I'm your AI sales assistant. Want to see if
    I can convince you to buy a pen? Just say the magic words!"

3. "Welcome! I've been practicing my sales pitch. When you're
    ready for me to sell you a pen, just let me know."
```

#### User Sells Mode - Greeting + Challenge
```
Variations based on difficulty:

EASY (Friendly Customer):
"Hi there! I'm Sarah, and I work in marketing. I've got a few
minutes before my next meeting. [pause] Sell me a pen."

MEDIUM (Skeptical Customer):
"Yeah, hi. I'm Mike. I'm pretty busy and I've already got a
drawer full of pens, but sure... [pause] Sell me a pen."

HARD (Tough Customer):
"Listen, I've heard every sales pitch in the book. I don't
need a pen, I don't want a pen, and I definitely don't have
time for this. But go ahead... [pause] Sell me a pen."

EXPERT (Wolf Mode):
"I'm Jordan. I've built companies, I've sold everything from
stocks to real estate. I know every trick in the book. So
impress me... [pause] Sell me a pen."
```

---

### Trigger Phrases for "Sell Me A Pen"

The AI should recognize these variations to trigger the sales pitch in AI Sells mode:

```javascript
const triggerPhrases = [
  "sell me a pen",
  "sell me the pen",
  "okay sell me a pen",
  "go ahead",
  "let's see it",
  "show me",
  "try to sell me",
  "give me your pitch",
  "let's hear it",
  "convince me",
  "I'm ready",
  "go for it",
  "do it",
  "start",
  "begin"
];
```

---

### AI Customer Personas (User Sells Mode)

#### Persona: The Busy Executive
```yaml
name: "Alex Chen"
occupation: "CEO"
personality: "Time-conscious, direct, values efficiency"
objections:
  - "I don't have time for this"
  - "My assistant handles office supplies"
  - "I use digital notes exclusively"
buying_triggers:
  - Efficiency gains
  - Status/luxury appeal
  - Time-saving features
```

#### Persona: The Budget Watcher
```yaml
name: "Pat Rivera"
occupation: "Accountant"
personality: "Frugal, analytical, needs justification"
objections:
  - "How much does it cost?"
  - "I can get pens for free at conferences"
  - "What's the ROI on a pen?"
buying_triggers:
  - Value proposition
  - Cost savings
  - Durability/longevity
```

#### Persona: The Skeptic
```yaml
name: "Jordan Blake"
occupation: "Lawyer"
personality: "Analytical, questions everything, hard to impress"
objections:
  - "I've heard this all before"
  - "What makes this different from any other pen?"
  - "I don't believe you"
buying_triggers:
  - Unique proof points
  - Credentials/testimonials
  - Logical arguments
```

#### Persona: The Friendly Buyer
```yaml
name: "Sam Martinez"
occupation: "Teacher"
personality: "Warm, open-minded, easy to talk to"
objections:
  - "I'm not sure I need another pen"
  - "Let me think about it"
buying_triggers:
  - Personal connection
  - Story/emotion
  - Practical benefits
```

---

### Objection Bank (User Sells Mode)

AI will randomly select from these based on difficulty:

```yaml
EASY:
  - "Hmm, I'm not sure..."
  - "I already have a pen though"
  - "What color does it come in?"

MEDIUM:
  - "I have a drawer full of pens already"
  - "Can't I just use my phone to take notes?"
  - "I got 50 free pens at the last trade show"
  - "That seems expensive for a pen"

HARD:
  - "I haven't used a pen in months"
  - "My company has a contract with a supplier"
  - "I'm not the decision maker for office supplies"
  - "I literally just bought pens yesterday"
  - "Why should I buy from you and not Amazon?"

EXPERT:
  - "Do you even know what I do? Why would I need your pen?"
  - "You're wasting my time. What's in it for me?"
  - "I've met a thousand salespeople. What makes you different?"
  - "Pens are commodities. Give me one reason to care."
  - "You've got 30 seconds before I walk out"
```

---

### Buying Signals

AI should recognize when user is doing well and progress toward "buying":

```yaml
positive_responses:
  - "That's interesting..."
  - "Tell me more about that"
  - "I hadn't thought of it that way"
  - "Okay, you've got my attention"
  - "What else can you tell me?"

closing_responses:
  - "Alright, you've convinced me"
  - "Fine, I'll take one"
  - "You know what? I'll buy it"
  - "That's actually a good point. I'm in."
```

---

## Technical Requirements

### State Management

```javascript
const sessionState = {
  mode: 'ai-sells' | 'user-sells',
  difficulty: 'easy' | 'medium' | 'hard' | 'expert',
  persona: PersonaObject,
  conversationHistory: [],
  currentPhase: 'greeting' | 'waiting' | 'pitching' | 'responding' | 'closing' | 'ended',
  metrics: {
    startTime: timestamp,
    turnCount: number,
    objectionsHandled: number,
    buyingSignalsTriggered: number
  },
  outcome: 'pending' | 'sale' | 'no-sale' | 'abandoned'
};
```

### Voice Recognition Requirements

- Real-time speech-to-text
- Trigger phrase detection
- Sentiment analysis (for scoring)
- Interruption handling

### Voice Synthesis Requirements

- Natural-sounding TTS
- Appropriate pacing for sales conversation
- Emotion/tone variation based on persona
- Pause handling for user response

---

## Scoring & Feedback (User Sells Mode)

### Scoring Criteria

| Criterion | Weight | Description |
|-----------|--------|-------------|
| **Opening** | 15% | Did user establish rapport before pitching? |
| **Need Discovery** | 25% | Did user ask questions to understand customer needs? |
| **Value Proposition** | 25% | Did user connect pen features to customer benefits? |
| **Objection Handling** | 20% | How well did user address concerns? |
| **Closing** | 15% | Did user ask for the sale confidently? |

### Score Calculation

```javascript
const calculateScore = (session) => {
  let score = 0;

  // Opening (15 points)
  if (session.askedCustomerName) score += 5;
  if (session.establishedRapport) score += 10;

  // Need Discovery (25 points)
  score += session.questionsAsked * 5; // up to 25

  // Value Proposition (25 points)
  if (session.connectedFeatureToBenefit) score += 15;
  if (session.personalizedPitch) score += 10;

  // Objection Handling (20 points)
  score += session.objectionsOvercome * 5; // up to 20

  // Closing (15 points)
  if (session.askedForSale) score += 10;
  if (session.outcome === 'sale') score += 5;

  return Math.min(score, 100);
};
```

### Feedback Output

```
┌─────────────────────────────────────────────────────────────┐
│                    SESSION RESULTS                           │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│   OUTCOME: SALE MADE! ✓                                     │
│   SCORE: 78/100                                             │
│                                                              │
│   ┌─────────────────────────────────────┐                   │
│   │ Opening          ████████░░  12/15  │                   │
│   │ Need Discovery   ██████████  25/25  │                   │
│   │ Value Prop       ██████░░░░  15/25  │                   │
│   │ Objections       ████████░░  16/20  │                   │
│   │ Closing          ████████░░  10/15  │                   │
│   └─────────────────────────────────────┘                   │
│                                                              │
│   STRENGTHS:                                                │
│   ✓ Great job asking discovery questions                    │
│   ✓ You handled the price objection well                    │
│                                                              │
│   AREAS TO IMPROVE:                                         │
│   → Try to connect features to specific customer needs      │
│   → Be more confident when asking for the sale              │
│                                                              │
│   [Try Again]  [Change Difficulty]  [Share Score]           │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

## Use Cases

### Use Case 1: Sales Training Platform
Companies can use this to train new salespeople in a low-pressure environment before real customer interactions.

### Use Case 2: Interview Preparation
Job candidates can practice the classic "sell me a pen" interview question with realistic feedback.

### Use Case 3: Entertainment/Viral Marketing
Visitors can challenge the AI and share their scores on social media, driving organic traffic.

### Use Case 4: Product Demonstration
Show potential customers how the AI sales agent works by having it sell to them.

### Use Case 5: Gamification
Leaderboards for highest scores, badges for completing challenges, daily streaks.

---

## Future Enhancements

1. **Multiple Products** - Not just pens, but any product from the catalog
2. **Video Mode** - Avatar-based visual interaction
3. **Multiplayer** - User vs User sales battles judged by AI
4. **Industry-Specific** - Custom personas for different industries (real estate, SaaS, etc.)
5. **Progress Tracking** - Track improvement over time
6. **Coaching Mode** - AI provides real-time tips during the pitch

---

## API Endpoints Needed

```
POST /api/sell-me-a-pen/start
  body: { mode: 'ai-sells' | 'user-sells', difficulty?: string }
  returns: { sessionId, greeting, persona? }

POST /api/sell-me-a-pen/respond
  body: { sessionId, userMessage }
  returns: { aiResponse, phase, metrics? }

POST /api/sell-me-a-pen/end
  body: { sessionId }
  returns: { outcome, score?, feedback? }

GET /api/sell-me-a-pen/leaderboard
  returns: { topScores[] }
```

---

## Document History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | December 2025 | Initial document creation |

---

*This feature transforms a classic sales challenge into an interactive training and entertainment experience.*
