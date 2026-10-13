# Vehicle module (ported from Safe-G KPK) - Phase 2

```bash
php spark migrate      # 2026-10-13-100000_CreateVehicleModule, 2026-10-13-130000_VehicleGroupsTransferDaily, 2026-10-13-140000_VehicleGroupColumnsRepair, 2026-10-13-150000_VehicleKpkAlignment
```
Turn it on per client: **Config > Client > Features > Vehicle Pass** (off by default; the platform admin always sees it).
Give roles access under **Config > Role > Vehicle**. The sidebar shows only what the role holds a key for.

## Sidebar / pages
| Menu | Page | Role key |
|---|---|---|
| Public > My Vehicle Pass | `vehicles/public` (staff + vendor passes) | my_vehicle_pass |
| Public > New Staff Request | `vehicles/public/staff-request` | new_staff_request |
| Public > Staff Vehicle Pass | `vehicles/public/staff` (staff passes only) | staff_vehicle_pass |
| Public > Request List | `vehicles/public/request-list` | public_request_list |
| Public > Approved / Reject List | `vehicles/public/reviewed-list` (one page, status filter) | public_approved_list or public_reject_list |
| Public > Closed List | `vehicles/public/closed-list` | public_closed_list |
| Cargo \| Non-Cargo | same pages under `vehicles/cnc/...` | cargo_* keys |
| Transfer Ownership | `vehicle-transfer` | transfer_ownership |
| Daily Pass (hidden) | `vehicle-daily/...` | my_vehicle_daily_pass, daily_pass_request_list, daily_pass_reject_list, daily_pass_closed_list |

* **Vendor accounts** use My Vehicle Pass (Public or Cargo): they only see passes of their own company, and the company is fixed. Cargo | Non-Cargo passes are always company vehicles.
* **Staff** passes (New Staff Request) pick a person from the `staff` table; they show in My Vehicle Pass and in Staff Vehicle Pass.
* **Vehicle Types** (Config) have a "Used for" setting: Public, Cargo | Non-Cargo, or both.
* **Renew**: an Issued pass can be renewed within 90 days of expiry (or after). The renewal is a new application; when its sticker is issued the old pass is terminated.
* **Transfer Ownership** (KPK): search another company's vehicle by plate; the company with the key takes it over immediately, no approval. A non-vendor user picks the receiving company. Expired passes cannot be taken over (apply for a new pass). Guarded against two companies grabbing the same vehicle (VEHICLE_ALREADY_CHANGED_OWNER). Every transfer is logged in `vehicle_transfers` (status Completed).
* **Daily Pass** is built but OFF. To switch it on add `VEHICLE_DAILY_PASS=true` to `.env`; the routes and the sidebar group appear together.

Flow: `Draft -> Pending -> Approved -> Issued -> Terminated` (Rejected can be edited and resubmitted).
Vehicle types (Config > Vehicle Types) decide which extra fields the form asks for, whether payment is needed, and whether the same plate may be on two passes.

Plate rule: one live pass per plate (across Public and Cargo | Non-Cargo; a renewal may reuse its own plate). A plate is free when its pass is Draft, Rejected, Terminated, or Issued but past its expiry.

## Payment (kept in the Java app)
VMS never talks to the bank. `App\Libraries\VehiclePayment` calls the Java service when these are set in `.env`:

```
VEHICLE_PAYMENT_URL = https://java-host/api/vms/payments
VEHICLE_PAYMENT_KEY = shared-secret          # sent as X-Api-Key
```
Empty URL = online payment off; staff type the receipt number when issuing the sticker.

Endpoints the Java app must offer (thin wrappers over its existing /api/fpx code):

| Call | Body / result |
|---|---|
| `POST {URL}/start` | `{order_no, amount, buyer_name, return_url}` -> `{ok:true, redirect_url, ref}` (redirect_url = the bank page; after paying the bank flow sends the browser to `return_url`) |
| `GET {URL}/status/{ref}` | `-> {ok:true, paid:true/false, receipt_no, payment_type}` |

After the browser returns to `vehicles/payment-return/{id}`, VMS calls `/status` and marks the pass Paid.
Not yet in VMS: amount per vehicle type (the `amount` column exists but is not filled in yet).

## KPK alignment (migration 150000)

* **Request form**: Application For, sticker year (next year offered from October), vehicle Operation (Cargo | Non-Cargo) filters the vehicle types, trailer weight / trailer type (20, 40, 45 Ft) per type flags, staff ID / department / HOD for staff, terms checkbox.
* **Plate rule** is checked per sticker year. An approver with the right key can edit a Pending request without changing its status.
* **Issue sticker**: serial (unique), date of issuance, collector IC, payment type CASH (receipt no.) or LEDGER (only when `companies.ledger_acc_no` is set; no receipt). Next Action column: Pending Approval / Proceed Payment / Collect Sticker.
* **Closed list tools** (Issued passes): Reissue (Damage / Lost / Stolen, history kept in `vehicle_reissues`), Change Serial No, Change Receipt, Change Vehicle Type, Change Locations. Routes `vehicles/(:num)/(reissue|change-serial|change-receipt|change-type|change-locations)`.
* **Config > Vehicle Types**: Operation select and Trailer Weight / Trailer Type flags.
* **Daily Pass** (still hidden): Individual or Staff, year made, colour, brand, road tax and insurance expiry, several visit dates (single day or range, stored as JSON in `visit_dates`), remark, terms.

Not ported yet: company vehicle limit, company permit check, e-mail notifications, print slip, terms stored in the database, configurable renewal months. `companies.ledger_acc_no` has no Config screen yet (set it in the database).
