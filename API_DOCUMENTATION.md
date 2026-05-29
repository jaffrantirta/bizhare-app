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
*Requires auth.* Upload identity document for KYC verification. Re-submitting resets status to `pending`.

**Request:** `multipart/form-data`
| Field | Type | Required | Description |
|---|---|---|---|
| id_type | enum: `ktp`, `sim`, `passport` | ✓ | Type of identity document |
| id_number | string (max 50) | ✓ | NIK / SIM number / passport number |
| id_photo | image file (max 5 MB) | ✓ | Photo of the identity document |
| selfie_photo | image file (max 5 MB) | | Selfie holding the identity document |
| full_name | string (max 255) | | Full name as printed on the document |
| place_of_birth | string (max 255) | | City / district of birth |
| date_of_birth | date (`YYYY-MM-DD`) | | Date of birth |
| phone_number | string (max 20) | | Active phone number |
| occupation | string (max 255) | | Current job / profession |
| marital_status | enum: `single`, `married`, `divorced`, `widowed` | | Marital status |
| province | string | | Province name (use values from Indonesian Regions reference below) |
| kabupaten | string | | Kabupaten / Kota name |
| kecamatan | string | | Kecamatan name |
| address | string (max 1000) | | Full street address |

> All fields except `id_type`, `id_number`, and `id_photo` are optional for backwards compatibility. Sending all fields is strongly recommended.

**Example Request**
```
POST /api/verification/upload
Content-Type: multipart/form-data

id_type=ktp
id_number=3271010101900001
id_photo=<file>
selfie_photo=<file>
full_name=Budi Santoso
place_of_birth=Bandung
date_of_birth=1990-01-01
phone_number=081234567890
occupation=Wiraswasta
marital_status=married
province=jawa_barat
kabupaten=Kab. Bandung
kecamatan=Dayeuhkolot
address=Jl. Soekarno Hatta No. 123, RT 02/RW 05
```

**Example Response (201)**
```json
{
  "success": true,
  "message": "ID verification documents uploaded successfully. Please wait for review.",
  "data": {
    "verification": {
      "id": 1,
      "id_type": "ktp",
      "id_number": "3271010101900001",
      "full_name": "Budi Santoso",
      "place_of_birth": "Bandung",
      "date_of_birth": "1990-01-01",
      "phone_number": "081234567890",
      "occupation": "Wiraswasta",
      "marital_status": "married",
      "province": "jawa_barat",
      "kabupaten": "Kab. Bandung",
      "kecamatan": "Dayeuhkolot",
      "address": "Jl. Soekarno Hatta No. 123, RT 02/RW 05",
      "status": "pending",
      "created_at": "2026-05-28T10:00:00Z"
    }
  }
}
```

> When an admin **approves** the verification, the user's `name` is automatically replaced with `full_name` from this submission.

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
| payment_method | enum: manual_transfer, gopay | ✓ |

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

**Example Response (201) — gopay**
```json
{
  "success": true,
  "data": {
    "transaction": { "id": 6, "amount": 375000, "status": "pending" },
    "snap_token": "d379aa71-99eb-4dd1-b9bb-eefe813746e9",
    "snap_redirect_url": "https://app.midtrans.com/snap/v3/redirection/d379aa71-..."
  }
}
```

> Use `snap_token` with the **Midtrans Snap SDK** (mobile) or open `snap_redirect_url` in a WebView. The Snap page handles GoPay and QRIS payment selection.

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

> **One investment per business** — an investor may only hold one active or pending investment per business. Attempting a second investment returns a 422 error. A rejected or cancelled investment does not block re-investment.

**Request Body**
| Field | Type | Required |
|---|---|---|
| payment_type | enum: `full`, `installment` | ✓ |
| tenure_months | integer (1–12) | required if `installment` |
| payment_method | enum: `manual_transfer`, `gopay` | ✓ |

**Error — already invested (422)**
```json
{ "success": false, "message": "You have already invested in this business." }
```

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
| payment_method | enum: manual_transfer, gopay | ✓ |

---

### GET /payments/{transaction}/status
*Requires auth.* Poll Snap payment status.

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
*Requires auth.* Paginated list of payment transactions (deposit, investment, installment).

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

## Portfolio

### GET /api/portfolio
*Requires verified investor.* Summary + full list of investor's business investments.

**Example Response (200)**
```json
{
  "success": true,
  "data": {
    "summary": {
      "total_investments": 3,
      "active_investments": 2,
      "pending_investments": 1,
      "total_invested": 3000000,
      "total_profit": 250000,
      "current_balance": 750000
    },
    "investments": [
      {
        "id": 1,
        "status": "active",
        "payment_type": "installment",
        "total_amount": 1500000,
        "admin_fee": 15000,
        "profit_received": 75000,
        "installment_progress": {
          "months_paid": 3,
          "tenure_months": 12,
          "remaining": 9,
          "next_due_date": "2026-07-01",
          "next_amount": 126250,
          "completed": false
        },
        "joined_at": "2026-01-15",
        "business": {
          "id": 1,
          "name": "Warung Makan Pak Haji",
          "category": "Food & Beverage",
          "location": "Jakarta Selatan",
          "status": "active",
          "image_url": "https://your-domain.com/storage/businesses/img.jpg",
          "current_investors": 8,
          "target_investors": 10,
          "activation_date": "2026-01-20"
        }
      }
    ]
  }
}
```

---

### GET /api/portfolio/{id}
*Requires verified investor.* Full detail of a single investment including installment schedule, profit history, and payment history.

**Example Response (200)**
```json
{
  "success": true,
  "data": {
    "investment": { ... },
    "installment_schedule": [
      {
        "month_number": 1,
        "amount": 126250,
        "status": "paid",
        "due_date": "2026-02-01",
        "paid_at": "2026-01-30T10:00:00Z"
      },
      {
        "month_number": 2,
        "amount": 126250,
        "status": "pending",
        "due_date": "2026-03-01",
        "paid_at": null
      }
    ],
    "profit_history": [
      {
        "id": 12,
        "amount": 75000,
        "notes": "Profit distribution for business: Warung Makan Pak Haji",
        "confirmed_at": "2026-04-01T08:00:00Z"
      }
    ],
    "payment_history": [
      {
        "id": 7,
        "type": "installment",
        "amount": 126250,
        "status": "success",
        "payment_method": "manual_transfer",
        "confirmed_at": "2026-01-30T10:00:00Z"
      }
    ]
  }
}
```

> `installment_schedule` is `null` for full-payment investments.

---

## Notifications

### GET /notifications
*Requires auth.* Paginated list of all notifications for the authenticated user.

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
        "id": "uuid-string",
        "type": "App\\Notifications\\TopUpSuccessNotification",
        "data": {
          "type": "topup_success",
          "title": "Top Up Berhasil",
          "message": "Top up sebesar Rp 375.000 telah dikonfirmasi.",
          "transaction_id": 5,
          "amount": 375000
        },
        "read_at": null,
        "created_at": "2026-05-28T10:00:00Z"
      }
    ],
    "total": 12,
    "per_page": 15,
    "current_page": 1
  }
}
```

**Notification `data` payload by type**

| `data.type` | Sent when | Key fields in `data` |
|---|---|---|
| `topup_success` | Initial deposit confirmed by admin | `transaction_id`, `amount` |
| `topup_failed` | Initial deposit rejected by admin | `transaction_id`, `amount` |
| `investment_payment_success` | Investment / installment payment confirmed | `transaction_id`, `payment_type`, `amount` |
| `investment_payment_failed` | Investment / installment payment rejected | `transaction_id`, `payment_type`, `amount` |
| `withdrawal_submitted` | Withdrawal request received | `withdrawal_id`, `amount`, `bank_name`, `account_number` |
| `withdrawal_approved` | Admin approves withdrawal | `withdrawal_id`, `amount` |
| `withdrawal_processed` | Admin marks withdrawal as sent | `withdrawal_id`, `amount`, `bank_name`, `account_number` |
| `withdrawal_rejected` | Admin rejects withdrawal (balance refunded) | `withdrawal_id`, `amount`, `notes` |
| `referral_reward` | Referral reward credited to balance | `transaction_id`, `amount`, `new_user_name`, `level` |
| `profit_received` | Profit distribution credited to balance | `transaction_id`, `amount`, `business_id`, `business_name` |
| `installment_reminder` | Installment due date approaching (scheduled) | `investment_id`, `installment_id`, `amount`, `due_date`, `month_number`, `business_name` |

> Every notification includes a `title` and `message` field suitable for display in push notification banners.

---

### POST /notifications/{id}/read
*Requires auth.* Mark a single notification as read.

**Example Response (200)**
```json
{ "success": true, "message": "Notification marked as read." }
```

---

### POST /notifications/mark-all-read
*Requires auth.* Mark all unread notifications as read.

**Example Response (200)**
```json
{ "success": true, "message": "All notifications marked as read." }
```

---

## Webhooks

### POST /payments/midtrans/callback
**Public** — called by Midtrans servers only. Do not call from mobile app.  
Verifies SHA-512 signature and updates transaction status automatically.  
Triggers referral rewards when an initial deposit is confirmed via Snap.

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

---

## Indonesian Regions Reference

Use these `province` key values in `POST /verification/upload`. The corresponding kabupaten/kota list is filtered by the selected province on the admin side.

| Key | Province Name |
|---|---|
| `aceh` | Aceh |
| `sumatera_utara` | Sumatera Utara |
| `sumatera_barat` | Sumatera Barat |
| `riau` | Riau |
| `jambi` | Jambi |
| `sumatera_selatan` | Sumatera Selatan |
| `bengkulu` | Bengkulu |
| `lampung` | Lampung |
| `bangka_belitung` | Kepulauan Bangka Belitung |
| `kepulauan_riau` | Kepulauan Riau |
| `dki_jakarta` | DKI Jakarta |
| `jawa_barat` | Jawa Barat |
| `jawa_tengah` | Jawa Tengah |
| `di_yogyakarta` | DI Yogyakarta |
| `jawa_timur` | Jawa Timur |
| `banten` | Banten |
| `bali` | Bali |
| `nusa_tenggara_barat` | Nusa Tenggara Barat |
| `nusa_tenggara_timur` | Nusa Tenggara Timur |
| `kalimantan_barat` | Kalimantan Barat |
| `kalimantan_tengah` | Kalimantan Tengah |
| `kalimantan_selatan` | Kalimantan Selatan |
| `kalimantan_timur` | Kalimantan Timur |
| `kalimantan_utara` | Kalimantan Utara |
| `sulawesi_utara` | Sulawesi Utara |
| `sulawesi_tengah` | Sulawesi Tengah |
| `sulawesi_selatan` | Sulawesi Selatan |
| `sulawesi_tenggara` | Sulawesi Tenggara |
| `gorontalo` | Gorontalo |
| `sulawesi_barat` | Sulawesi Barat |
| `maluku` | Maluku |
| `maluku_utara` | Maluku Utara |
| `papua_barat` | Papua Barat |
| `papua_barat_daya` | Papua Barat Daya |
| `papua` | Papua |
| `papua_selatan` | Papua Selatan |
| `papua_tengah` | Papua Tengah |
| `papua_pegunungan` | Papua Pegunungan |

For `kabupaten`, send the full label string (e.g. `"Kab. Bandung"`, `"Kota Surabaya"`). For `kecamatan`, send free text.

---

## Marital Status Reference

| Value | Label |
|---|---|
| `single` | Belum Menikah |
| `married` | Menikah |
| `divorced` | Cerai Hidup |
| `widowed` | Cerai Mati |
