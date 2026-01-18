# Payment Gateway Options for a Small Apparel Website  
*Accepting credit cards + Apple Pay + Google Pay*  

_Last updated: November 28, 2025_  

---

## 1. What you want to support

You said you want to accept:

- All major credit/debit cards (Visa, Mastercard, Amex, Discover, etc.)
- Apple Pay
- Google Pay

You’ll do this through a **payment gateway / processor** that provides:

- A hosted checkout page or UI components you can drop into your site  
- Support for the wallets you want (Apple Pay + Google Pay)  
- Simple APIs (for custom flows)  
- PCI-DSS compliance and good fraud tools  

---

## 2. Shortlist: Good choices for a small apparel site

All of these support major cards and digital wallets like Apple Pay and Google Pay, though **availability can depend on your country and business type**.

### 2.1 Stripe (great default for most small e‑commerce sites)

- **What it is:** All‑in‑one payment platform with strong developer tools and great docs.  
- **Payment methods:** Supports all major cards plus Apple Pay and Google Pay, along with 100+ other methods. citeturn0search8turn0search16  
- **Wallets:** Apple Pay & Google Pay with minimal setup; Stripe is designed to make activating them straightforward. citeturn0search0turn0search4  
- **Fees:** Typically flat % + fixed fee per transaction (varies by country; in the US it’s often around ~2.9% + $0.30 for card payments).  
- **Pros:**  
  - Simple dashboard & reporting  
  - Excellent docs & SDKs (JS, PHP, Node, Python, etc.)  
  - Built‑in fraud tools and subscriptions  
- **Cons:**  
  - Slight learning curve if you build a fully custom integration  
  - Some advanced features can feel overkill for very tiny sites  

**When Stripe is best:**  
- You control your own checkout or use a generic cart solution.  
- You want good docs and easy developer experience.  

---

### 2.2 Braintree (by PayPal)

- **What it is:** Full‑featured gateway by PayPal that lets you accept cards, PayPal, Venmo, and digital wallets.  
- **Payment methods:** Credit/debit cards, PayPal, Venmo (US), plus Apple Pay and Google Pay. citeturn0search9turn0search13turn0search17turn0search25  
- **Fees:** Similar structure to Stripe (flat % + fixed fee; varies by region).  
- **Pros:**  
  - Single integration for cards **and PayPal** (big trust boost for buyers). citeturn0search1  
  - Good for multi‑method checkouts in one place.  
- **Cons:**  
  - Docs and dashboard are a bit more complex than Stripe for some people.  

**When Braintree is best:**  
- You know you want **PayPal + cards + Apple/Google Pay** in one integration.  
- You’re comfortable doing a slightly more involved setup.  

---

### 2.3 Square Online Payments

- **What it is:** Payments + POS ecosystem. Good if you also sell **in person** (pop‑ups, markets, etc.) using Square hardware.  
- **Payment methods:** Cards, digital wallets (Apple Pay, Google Pay, PayPal in some setups), ACH/bank in certain regions. citeturn0search18turn0search10  
- **Wallets:** Apple Pay and Google Pay supported on compatible Square online stores / checkouts. citeturn0search2turn0search14turn0search22  
- **Pros:**  
  - Unifies in‑person and online sales.  
  - Easy hosted checkout with minimal coding.  
- **Cons:**  
  - Less “developer‑centric” if you want a deeply custom flow.  
  - Best when you buy into more of the Square ecosystem.  

**When Square is best:**  
- You already use (or plan to use) **Square card readers** or Square POS.  
- You’re happy with a more “plug‑and‑play” site & checkout.  

---

### 2.4 Adyen (powerful, more enterprise‑y)

- **What it is:** Enterprise‑grade payments platform with global coverage.  
- **Payment methods:** Cards, local payment methods, and digital wallets including Apple Pay & Google Pay. citeturn0search11turn0search19turn0search23turn0search7  
- **Pros:**  
  - Very strong global capabilities and routing optimizations (can lower processing costs). citeturn0search3turn0search15  
- **Cons:**  
  - Overkill for many very small shops.  
  - Often targets larger merchants with higher volume.  

**When Adyen is best:**  
- You see yourself scaling to a **large, international brand** fairly quickly.  

---

## 3. Quick comparison (cards + Apple Pay + Google Pay)

| Gateway   | Major Cards | Apple Pay | Google Pay | PayPal Option | Best Fit for You? |
|----------|-------------|-----------|------------|---------------|-------------------|
| **Stripe**   | Yes         | Yes       | Yes        | Indirect (via partner tools or separate PayPal) | Great all‑around starter choice |
| **Braintree**| Yes         | Yes       | Yes        | **Yes (native)** | Great if you want PayPal + cards + wallets together |
| **Square**   | Yes         | Yes*      | Yes*       | Sometimes (depends on configuration) | Great if you also sell in person |
| **Adyen**    | Yes         | Yes       | Yes        | Via configuration | Better for higher‑volume brands |

\*Square’s Apple/Google Pay support depends on using their compatible online store / checkout setup. citeturn0search2turn0search10turn0search18  

---

## 4. Security and compliance basics

Regardless of which gateway you choose:

- **Use HTTPS** everywhere (valid TLS certificate).  
- The gateway should handle **card data** so it **never touches your server** → this keeps PCI burden low.  
- Turn on **fraud protection** options (3D Secure / SCA where applicable). citeturn0search13turn0search16  
- Educate yourself about digital‑wallet scams (people being tricked into sharing one‑time passcodes) and teach staff to never ask customers for OTPs. citeturn0news38  

---

## 5. Recommended starting strategy

For a small apparel site that wants **cards + Apple Pay + Google Pay**, a simple plan is:

1. **Start with Stripe or Braintree.**  
   - Stripe if you don’t care much about PayPal.  
   - Braintree if you want **PayPal + cards + wallets** in one integration.  
2. Use their **pre‑built checkout** components (Stripe Checkout or Braintree Drop‑in) instead of building from scratch.  
3. Add **Apple Pay & Google Pay** via their guides (usually just extra config + domain verification). citeturn0search0turn0search4turn0search11turn0search19  

Below are concrete code examples so you (or a developer) can see what an integration actually looks like.

---

## 6. Example: Stripe (cards + Apple Pay + Google Pay)

Stripe’s usual pattern:

1. Your frontend calls your backend to **create a PaymentIntent**.  
2. Your backend uses Stripe’s secret key to create that PaymentIntent.  
3. The frontend confirms the payment via Stripe.js (card input or Wallet button).  

### 6.1 Backend example (Node.js + Express)

```bash
npm install stripe express
```

```js
// server.js
import express from "express";
import Stripe from "stripe";

const app = express();
app.use(express.json());

const stripe = new Stripe(process.env.STRIPE_SECRET_KEY); // e.g. "sk_live_..."
const DOMAIN = "https://your-apparel-site.com";

// Create a PaymentIntent for a specific order
app.post("/create-payment-intent", async (req, res) => {
  try {
    const { amount, currency } = req.body;
    // amount is in the smallest currency unit (e.g. cents)
    const paymentIntent = await stripe.paymentIntents.create({
      amount,
      currency,
      automatic_payment_methods: { enabled: true }, // supports cards + wallets
    });
    res.json({ clientSecret: paymentIntent.client_secret });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Unable to create payment intent" });
  }
});

app.listen(4242, () => console.log("Server running on port 4242"));
```

### 6.2 Frontend example: Card form (Stripe Elements)

Include Stripe.js and then mount card input elements:

```html
<!-- Checkout page -->
<script src="https://js.stripe.com/v3/"></script>

<form id="payment-form">
  <div id="card-element"><!-- Stripe will mount the card UI here --></div>
  <button id="submit">Pay</button>
  <div id="error-message"></div>
</form>

<script>
  const stripe = Stripe("pk_live_YOUR_PUBLISHABLE_KEY");
  const elements = stripe.elements();
  const cardElement = elements.create("card");
  cardElement.mount("#card-element");

  const form = document.getElementById("payment-form");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    // 1. Create PaymentIntent on your server
    const amount = 4999; // e.g. $49.99 → 4999 cents
    const response = await fetch("/create-payment-intent", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ amount, currency: "usd" }),
    });
    const { clientSecret } = await response.json();

    // 2. Confirm card payment
    const { error, paymentIntent } = await stripe.confirmCardPayment(
      clientSecret,
      {
        payment_method: {
          card: cardElement,
        },
      }
    );

    if (error) {
      document.getElementById("error-message").textContent = error.message;
    } else if (paymentIntent.status === "succeeded") {
      // TODO: show success message, update order, redirect, etc.
      alert("Payment successful!");
    }
  });
</script>
```

This handles **all major credit cards** with minimal HTML + JS.

---

### 6.3 Stripe Payment Request Button (Apple Pay + Google Pay)

Stripe’s **Payment Request Button** uses Apple Pay / Google Pay automatically when available on the device/browser.

**Key notes:**  
- Works in browsers that support the respective wallets (Safari for Apple Pay; Chrome/Android for Google Pay). citeturn0search0turn0search4turn0search20turn0search24  
- You must complete **domain verification for Apple Pay** in your Stripe dashboard and have proper configuration for Google Pay.

```html
<div id="payment-request-button"></div>
<div id="error-message"></div>

<script>
  const stripe = Stripe("pk_live_YOUR_PUBLISHABLE_KEY");
  const paymentRequest = stripe.paymentRequest({
    country: "US",
    currency: "usd",
    total: {
      label: "Order Total",
      amount: 4999, // 49.99 USD
    },
    requestPayerName: true,
    requestPayerEmail: true,
  });

  const elements = stripe.elements();
  const prButton = elements.create("paymentRequestButton", {
    paymentRequest,
  });

  // Check if the browser supports Payment Request (Apple Pay / Google Pay)
  paymentRequest.canMakePayment().then(function (result) {
    if (result) {
      prButton.mount("#payment-request-button");
    } else {
      document.getElementById("payment-request-button").style.display = "none";
    }
  });

  paymentRequest.on("paymentmethod", async (ev) => {
    try {
      // Create PaymentIntent on your server
      const response = await fetch("/create-payment-intent", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ amount: 4999, currency: "usd" }),
      });
      const { clientSecret } = await response.json();

      const { error, paymentIntent } = await stripe.confirmCardPayment(
        clientSecret,
        {
          payment_method: ev.paymentMethod.id,
        },
        { handleActions: false }
      );

      if (error) {
        ev.complete("fail");
        document.getElementById("error-message").textContent = error.message;
      } else {
        ev.complete("success");
        if (paymentIntent.status === "requires_action") {
          await stripe.confirmCardPayment(clientSecret);
        }
        // Payment complete!
        alert("Wallet payment successful!");
      }
    } catch (err) {
      console.error(err);
      ev.complete("fail");
    }
  });
</script>
```

With this, customers on supported devices see **Apple Pay**, **Google Pay**, or other wallet options as a single “Pay” button.

---

## 7. Example: Braintree Drop‑in UI (cards + PayPal + wallets)

Braintree’s Drop‑in UI lets you accept **cards + PayPal + Apple Pay/Google Pay** (if enabled on your account) with one frontend component. citeturn0search1turn0search5turn0search13  

### 7.1 Backend example (Node.js) – generating a client token

```bash
npm install braintree express
```

```js
// server-braintree.js
import express from "express";
import braintree from "braintree";

const app = express();
app.use(express.json());

const gateway = new braintree.BraintreeGateway({
  environment: braintree.Environment.Sandbox, // or Production
  merchantId: process.env.BT_MERCHANT_ID,
  publicKey: process.env.BT_PUBLIC_KEY,
  privateKey: process.env.BT_PRIVATE_KEY,
});

// 1. Provide client token to the browser
app.get("/client-token", async (req, res) => {
  try {
    const response = await gateway.clientToken.generate({});
    res.send(response.clientToken);
  } catch (err) {
    console.error(err);
    res.status(500).send("Unable to generate client token");
  }
});

// 2. Create a transaction using the payment method nonce from the browser
app.post("/checkout", async (req, res) => {
  try {
    const { paymentMethodNonce, amount } = req.body;
    const result = await gateway.transaction.sale({
      amount,
      paymentMethodNonce,
      options: { submitForSettlement: true },
    });

    if (result.success) {
      res.json({ success: true, transactionId: result.transaction.id });
    } else {
      res.status(400).json({ success: false, error: result.message });
    }
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false, error: "Server error" });
  }
});

app.listen(3000, () => console.log("Braintree server running on port 3000"));
```

### 7.2 Frontend example – Drop‑in UI

```html
<script src="https://js.braintreegateway.com/web/dropin/1.43.0/js/dropin.min.js"></script>

<div id="dropin-container"></div>
<button id="submit-button">Pay</button>

<script>
  // 1. Fetch client token
  fetch("/client-token")
    .then((res) => res.text())
    .then((clientToken) => {
      // 2. Create drop-in instance
      braintree.dropin.create(
        {
          authorization: clientToken,
          container: "#dropin-container",
          // If Apple Pay / Google Pay is enabled on your Braintree account,
          // those options will appear automatically.
          paypal: { flow: "vault" },
        },
        function (createErr, instance) {
          if (createErr) {
            console.error(createErr);
            return;
          }

          const button = document.getElementById("submit-button");
          button.addEventListener("click", function () {
            instance.requestPaymentMethod(function (err, payload) {
              if (err) {
                console.error(err);
                return;
              }

              // 3. Send nonce to server
              fetch("/checkout", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                  paymentMethodNonce: payload.nonce,
                  amount: "49.99",
                }),
              })
                .then((res) => res.json())
                .then((data) => {
                  if (data.success) {
                    alert("Payment successful! Transaction ID: " + data.transactionId);
                  } else {
                    alert("Payment failed: " + data.error);
                  }
                })
                .catch(console.error);
            });
          });
        }
      );
    });
</script>
```

Once Apple Pay / Google Pay are enabled in your Braintree control panel and configured for your domain, they can appear in this Drop‑in UI as additional options.

---

## 8. What to actually do next (step‑by‑step)

1. **Pick a gateway**  
   - If you’re undecided, start with **Stripe** (simplest docs) or **Braintree** (if PayPal is important).  

2. **Create a sandbox/test account**  
   - Enable **Apple Pay & Google Pay** in the dashboard and follow the domain verification instructions.  

3. **Implement the simplest flow first**  
   - Stripe: use **Stripe Checkout** or basic card Elements.  
   - Braintree: use **Drop‑in UI**.  

4. **Test with real devices**  
   - Test Apple Pay on a real iPhone/Safari + a card in Apple Wallet.  
   - Test Google Pay on Android / Chrome with a test card in Google Pay.  

5. **Switch to live keys and go live**  
   - Replace test keys with live keys.  
   - Make a few small real payments to yourself.  

6. **Monitor and tweak**  
   - Watch your dashboard for disputes or failed payments.  
   - Turn on extra fraud controls if needed.  

---

If you tell me:
- what platform you’re using now (pure custom HTML/CSS/JS, Shopify, WooCommerce, etc.), and  
- what country your business is in,  

I can tailor this to **one concrete “do exactly this” plan** for your specific apparel site.
