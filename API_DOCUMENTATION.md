# API Documentation

**Base URL:** `http://your-domain.com/api`  
**Authentication:** Bearer token via Laravel Sanctum  
**Content-Type:** `application/json`

All authenticated requests require:
```
Authorization: Bearer {token}
```

---

## Auth

### POST /register
Register a new user. Returns auth token and auto-generated referral code.

**Request Body**
| Field | Type | Required | Description |
|---|---|---|---|
| name | string | ✓ | Full name |
| email | string | ✓ | Unique email address |
| password | string | ✓ | Min 8 chars |
| password_confirmation | string | ✓ | Must match password |
| phone | string | | Mobile number |
| referral_code | string | | Existing user's referral code |

**Example Request**
```json
{
  "name": "Budi Santoso",
  "email": "budi@example.com",
  "password": "secret123",
  "password_confirmation": "secret123",
  "phone": "081234567890",
  "referral_code": "jaffran_x7k2"
}
```

**Example Response (201)**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": { "id": 2, "name": "Budi Santoso", "email": "budi@example.com" },
    "token": "1|abc123...",
    "token_type": "Bearer",
    "referral_code": "budisantoso_mn4p"
  }
}
```

---

### POST /login

**Request Body**
| Field | Type | Required |
|---|---|---|
| email | string | ✓ |
| password | string | ✓ |

**Example Response (200)**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": { "id": 2, "name": "Budi Santoso", "email": "budi@example.com" },
    "token": "2|xyz789...",
    "token_type": "Bearer"
  }
}
```

**Error (401)**
```json
{ "success": false, "message": "Invalid email or password" }
```

---

### POST /logout
*Requires auth.*

**Example Response (200)**
```json
{ "success": true, "message": "Logged out successfully" }
```

---

### GET /profile
*Requires auth.* Returns authenticated user with ID verification status.

---

### PUT /profile
*Requires auth.*

**Request Body** (all optional)
| Field | Type |
|---|---|
| name | string |
| phone | string |
| password | string |
| password_confirmation | string |

---

## Onboarding

### POST /verification/upload
*Requires auth.* Upload identity document for KYC verification.

**Request:** `multipart/form-data`
| Field | Type | Required |
|---|---|---|
| id_type | enum: ktp, sim, passport | ✓ |
| id_number | string | ✓ |
| id_photo | image file (max 5 MB) | ✓ |
| selfie_photo | image file (max 5 MB) | |

**Example Response (201)**
```json
{
  "success": true,
  "message": "ID verification documents uploaded successfully. Please wait for review.",
  "data": {
    "verification": { "id": 1, "id_type": "ktp", "status": "pending" }
  }
}
```

---

### GET /verification/status
*Requires auth.* Check onboarding progress.

**Example Response (200)**
```json
{
  "success": true,
  "data": {
    "is_verified": false,
    "verification_status": "pending",
    "has_initial_deposit": false,
    "steps": {
      "id_uploaded": true,
      "id_approved": false,
      "deposit_paid": false
    }
  }
}
```

---

### POST /initial-deposit/pay
*Requires auth.* Submit initial deposit payment (amount configurable by admin, default IDR 375,000).

**Request Body**
| Field | Type | Required |
|---|---|---|
| payment_method | enum: manual_transfer, qris | ✓ |

**Example Response (201) — manual_transfer**
```json
{
  "success": true,
  "message": "Initial deposit initiated. Please complete your payment.",
  "data": {
    "transaction": { "id": 5, "amount": 375000, "status": "pending" },
    "bank_details": {
      "bank_name": "BCA",
      "account_number": "1234567890",
      "account_name": "My App",
      "amount": 375000
    }
  }
}
```

**Example Response (201) — qris**
```json
{
  "data": {
    "transaction": {
      "id": 6,
      "amount": 375000,
      "status": "pending",
      "midtrans_qr_code_url": "https://api.midtrans.com/v2/qris/...",
      "midtrans_deeplink_url": "https://gojek.com/..."
    }
  }
}
```

---

### POST /payments/manual
*Requires auth.* Upload proof of manual bank transfer.

**Request:** `multipart/form-data`
| Field | Type | Required |
|---|---|---|
| transaction_id | integer | ✓ |
| proof_image | image file (max 5 MB) | ✓ |

**Example Response (200)**
```json
{
  "success": true,
  "message": "Payment proof uploaded. Waiting for admin confirmation."
}
```

---

## Businesses

### GET /businesses
*Requires auth.* List open/active businesses.

**Query Parameters**
| Param | Description |
|---|---|
| per_page | Items per page (default: 15) |

**Example Response (200)**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Warung Makan Pak Haji",
        "category": "Food & Beverage",
        "status": "open",
        "current_investors": 3,
        "target_investors": 10
      }
    ],
    "total": 1, "per_page": 15, "current_page": 1
  }
}
```

---

### GET /businesses/{id}
*Requires auth.* Single business detail.

---

### POST /businesses/{id}/invest
*Requires verified investor.*

**Request Body**
| Field | Type | Required |
|---|---|---|
| payment_type | enum: full, installment | ✓ |
| tenure_months | integer (1–12) | required if installment |
| payment_method | enum: manual_transfer, qris | ✓ |

**Example Response (201)**
```json
{
  "success": true,
  "message": "Investment created successfully",
  "data": {
    "investment": {
      "id": 3, "payment_type": "installment",
      "total_amount": 1500000, "tenure_months": 12, "status": "pending"
    },
    "transaction": { "id": 7, "amount": 125000, "status": "pending" }
  }
}
```

---

## Payments

### POST /payments/installment/{investment}
*Requires verified investor.* Pay next monthly installment.

**Request Body**
| Field | Type | Required |
|---|---|---|
| payment_method | enum: manual_transfer, qris | ✓ |

---

### GET /payments/{transaction}/status
*Requires verified investor.* Poll QRIS payment status.

**Example Response (200)**
```json
{
  "data": {
    "transaction": { "id": 7, "status": "success", "confirmed_at": "2026-05-28T10:00:00Z" }
  }
}
```

---

### GET /payments/history
*Requires verified investor.* Paginated list of payment transactions (deposit, investment, installment).

---

## Balance & Withdrawal

### GET /balance
*Requires verified investor.*

**Example Response (200)**
```json
{
  "success": true,
  "data": {
    "balance": 850000,
    "breakdown": {
      "investment_profit": 700000,
      "referral_reward": 150000
    },
    "is_verified": true,
    "has_initial_deposit": true
  }
}
```

> `balance` is the current withdrawable amount. `breakdown` shows total ever earned from each source.

---

### GET /balance/history
*Requires verified investor.* All transactions affecting balance.

**Query Parameters**
| Param | Description |
|---|---|
| type | Filter by type: profit, referral_reward, withdrawal, initial_deposit, etc. |
| per_page | Default: 15 |

---

### POST /withdrawal/request
*Requires verified investor.*

**Request Body**
| Field | Type | Required |
|---|---|---|
| amount | number | ✓ |
| bank_name | string | ✓ |
| account_number | string | ✓ |
| account_name | string | ✓ |

**Example Response (201)**
```json
{
  "message": "Withdrawal request submitted successfully.",
  "data": {
    "withdrawal": {
      "id": 1,
      "amount": 100000,
      "bank_name": "BCA",
      "account_number": "0987654321",
      "status": "pending"
    }
  }
}
```

**Error — insufficient balance (422)**
```json
{ "success": false, "message": "Insufficient balance. Available: 50.000" }
```

---

### GET /withdrawal/history
*Requires verified investor.* Paginated withdrawal request history.

---

## Referral

### GET /referral/code
*Requires verified investor.*

**Example Response (200)**
```json
{
  "data": {
    "referral_code": "budisantoso_mn4p",
    "total_referrals": 7,
    "total_rewarded": 259000
  }
}
```

---

### GET /referral/tree
*Requires verified investor.* Levels 1–3 show individual detail; deeper levels show aggregate count only.

**Example Response (200)**
```json
{
  "data": {
    "referral_code": "budisantoso_mn4p",
    "direct_referrals": 3,
    "tree": {
      "level_1": [
        { "name": "Andi", "joined_at": "2026-01-10", "has_initial_deposit": true },
        { "name": "Sari", "joined_at": "2026-02-14", "has_initial_deposit": false }
      ],
      "level_2": [
        { "name": "Rudi", "joined_at": "2026-03-01", "has_initial_deposit": true }
      ],
      "level_3": [],
      "deeper_levels": { "total_count": 4 }
    }
  }
}
```

---

### GET /referral/rewards
*Requires verified investor.* Paginated referral reward history.

**Example Response (200)**
```json
{
  "data": {
    "data": [
      {
        "id": 12,
        "amount": 37000,
        "status": "success",
        "from_user": "Andi",
        "level": 1,
        "created_at": "2026-01-10T09:00:00Z"
      }
    ]
  }
}
```

---

## Notifications

### GET /notifications
*Requires verified investor.* Paginated list including installment reminders and system notifications.

**Example Response (200)**
```json
{
  "data": {
    "data": [
      {
        "id": "uuid",
        "type": "App\\Notifications\\InstallmentReminderNotification",
        "data": {
          "business_name": "Warung Makan Pak Haji",
          "due_date": "2026-06-01",
          "amount": 126250,
          "month_number": 2
        },
        "read_at": null,
        "created_at": "2026-05-29T08:00:00Z"
      }
    ]
  }
}
```

---

### POST /notifications/{id}/read
*Requires verified investor.* Mark a single notification as read.

---

### POST /notifications/mark-all-read
*Requires verified investor.* Mark all notifications as read.

---

## Webhooks

### POST /payments/midtrans/callback
**Public** — called by Midtrans servers only. Do not call from mobile app.  
Verifies SHA-512 signature and updates transaction status automatically.  
Triggers referral rewards when an initial deposit is confirmed via QRIS.

---

## Error Responses

| HTTP Code | Meaning |
|---|---|
| 400 | Bad request / business logic error |
| 401 | Missing or invalid Bearer token |
| 403 | Forbidden — e.g. identity not verified or initial deposit not completed |
| 404 | Resource not found |
| 422 | Validation failed |
| 500 | Server error |

**Validation Error (422)**
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

---

## Transaction Types Reference

| Type | Description |
|---|---|
| initial_deposit | One-time onboarding deposit |
| investment | Full-payment investment |
| installment | Monthly installment payment |
| profit | Investment profit distributed by admin |
| referral_reward | Reward from multi-level referral chain |
| withdrawal | Balance withdrawal request |
| refund | Refunded payment |
