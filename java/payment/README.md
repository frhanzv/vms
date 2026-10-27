# Bank payment (PayNet FPX), Java

The bank transaction code stays in Java. It's copied from KPK Safe-G (`safeg_kpk`, commit `1d72bc62`) with the same package `my.com.pymsoft.safe_g_security`, so it can be compared with KPK file by file.

The PHP side never talks to the bank. It only calls this service through [`app/Libraries/VehiclePayment.php`](../../app/Libraries/VehiclePayment.php) (`.env`: `VEHICLE_PAYMENT_URL`, `VEHICLE_PAYMENT_KEY`).

## What is here

| Folder | Files | Job |
|---|---|---|
| `config/` | `FpxKeyLoader`, `FpxSigner`, `FpxSupportedFile` | Load the merchant key, sign requests, verify the bank's reply |
| `controller/` | `PayNetController` (`/api/fpx/*`) | Bank list, start a payment (`/authorize`), result page (`/result`) |
| `controller/` | `PaymentController` (`/api/payment/*`) | Bank callback (`/transaction`) and the result page |
| `service/` | `PayNetFpxService`, `Impl/PayNetFpxServiceImpl` | The FPX logic |
| `domain/`, `repo/` | `FpxTransaction`, `FPXIntegrationTransaction`, `FpxBanks`, `PaymentHeader`, `PaymentDetail`, lookups | Tables and their repositories |
| `request/`, `response/`, `view/` | Forms, response codes, JSON views | Supporting classes |

## Differences from KPK

- `FpxKeyLoader` reads the keystore password from the **`FPX_KEYSTORE_PASSWORD`** environment variable. KPK has it written in the code.
- No keys or certificates are included. `.gitignore` blocks them.

## Not working on its own yet

1. **No build file.** The classes belong to KPK's Spring Boot app (`pom.xml` there). To run here, they need a small Spring Boot project around them.
2. **`PaymentController` calls `VendorPassService.updatePaymentStatus(...)`**, which isn't copied: it's KPK's vendor pass service. In VMS this is the point where the paid application gets marked as **Paid**.
3. **The API doesn't match VMS yet.** `VehiclePayment.php` expects a start call and a status call that return JSON. KPK exposes `/api/fpx/authorize` and `/api/fpx/result`. One of the two sides has to be adapted.

## Needed on the server (not in git)

On the classpath (`src/main/resources/`):
- `merchant.p12`, the merchant keystore
- the PayNet certificates the code reads (`EX00030709.cer`, `fpxuat.cer`, `fpxuat_current.cer`, `FPXPublic.cer`)

Environment variable:
- `FPX_KEYSTORE_PASSWORD`

Get these files from the KPK server or the PayNet account owner.
