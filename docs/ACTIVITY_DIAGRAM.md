# Activity Diagram - Aspirasi Siswa Application

## Overall System Flow

```
                            ┌─────────────┐
                            │   START     │
                            └──────┬──────┘
                                   │
                    ┌──────────────┴──────────────┐
                    │                             │
              ┌─────v────┐                 ┌─────v────┐
              │  STUDENT │                 │  ADMIN   │
              └─────┬────┘                 └─────┬────┘
                    │                             │
        ┌───────────v─────────────┐    ┌─────────v──────────────┐
        │   Login ke Workspace    │    │  Login Admin Panel     │
        └───────────┬─────────────┘    └─────────┬──────────────┘
                    │                             │
        ┌───────────v─────────────────────────────┴───────────────┐
        │                                                         │
        │            ╔═══════════════════════════════╗            │
        │            ║   ASPIRASIKU WORKSPACE UI     ║            │
        │            ║ (History Left + Compose Right)║            │
        │            ╚═══════════════════════════════╝            │
        │                                                         │
        └─┬───────────────────────────────────────────────────────┘
          │
          ├──────────────┬────────────────────────────────────────┐
          │              │                                        │
          v              v                                        v
    ┌─────────┐    ┌─────────────────┐                  ┌──────────────────┐
    │View Old │    │Click New Chat & │                  │ Filter + Sort:   │
    │History  │    │  Compose        │                  │ Mode, Category,  │
    │─────────│    │─────────────────│                  │ Status, Progress │
    │- Check  │    │┌───────────────┐│                  │──────────────────│
    │  status │    ││ Choose Mode:  ││                  │- Custom shows    │
    │- View   │    ││ ○ Template    ││                  │  ANTREAN ranking │
    │  thread │    ││ ○ Custom      ││                  │- Vote count for  │
    │- See    │    │└───────┬───────┘│                  │  priority badge  │
    │  rating │    │        │        │                  │- Export Excel/PDF
    │  form   │    │   ┌────┴────┐   │                  └──────────────────┘
    │ (if     │    │   │          │   │                       │
    │Selesai) │    │   v          v   │                       │
    └────┬────┘    │┌──────┐  ┌──────┐                       │
         │         ││TEMPLATE││CUSTOM│                       │
         │         │└──┬───┘  └──┬───┘                       │
         │         │   │          │   │                       │
         │         │   v          v   │                       │
         │         │┌────────────────┐│                       │
         │         ││Fill Form Step- ││                       │
         │         ││by-Step (AI Bot)││                       │
         │         │├────────────────┤│                       │
         │         ││Template:       ││                       │
         │         ││- Title         ││                       │
         │         ││- Category      ││                       │
         │         ││- Location      ││                       │
         │         ││- Description   ││                       │
         │         ││- Photo         ││                       │
         │         ││                ││                       │
         │         ││Custom:         ││                       │
         │         ││- Title         ││                       │
         │         ││- Custom Loc    ││                       │
         │         ││- Priority Expl.││                       │
         │         ││- Description   ││                       │
         │         ││- Photo         ││                       │
         │         │└────────┬───────┘│                       │
         │         │         │        │                       │
         │         │    ┌────v────┐   │                       │
         │         │    │  SUBMIT │   │                       │
         │         │    └────┬────┘   │                       │
         │         │         │        │                       │
         │         └────┬────┴────┬───┘                       │
         │              │         │                           │
         └──────────┬───┴─────────┴─────────┬────────────────┘
                    │                       │
                    v                       v
         ┌──────────────────┐    ┌───────────────────────┐
         │INPUT_ASPIRATIONS │    │ASPIRASI LIST VIEW     │
         │Table Created     │    │────────────────────── │
         │- status: menunggu│    │Filter + Sort          │
         │- input_at: now   │    │- See all pending      │
         │- mode: template/ │    │- Separate custom queue
         │  custom          │    │- ANTREAN rank for     │
         │                  │    │  custom mode          │
         │                  │    └───────┬───────────────┘
         │                  │            │
         │  NOTIFICATION ◄──┤            ├─ Submit loaded
         │  "New aspirasi"   │            │
         │                  │            v
         │                  │    ┌──────────────────────┐
         │                  │    │ Choose Action        │
         │                  │    │────────────────────  │
         │                  │    │ ○ View Detail        │
         │                  │    │ ○ Approve            │
         │                  │    │ ○ Reject             │
         │                  │    │ ○ Export             │
         │                  │    └──────┬───────────────┘
         │                  │           │
         └──────────────────┴───────┬───┼──────────────┐
                                    │   │              │
                                    v   v              v
                            ┌───────────────┐  ┌─────────────┐
                            │ [APPROVE]     │  │  [REJECT]   │
                            │───────────────│  │─────────────│
                            │Create record: │  │Update status│
                            │ASPIRATIONS    │  │= ditolak    │
                            │status=diterima│  │Add reason   │
                            │progress_status│  │Send notif   │
                            │=Belum Dimulai │  │END (student │
                            │Send notif     │  │ sees thread │
                            │               │  │ with reason)│
                            └────────┬──────┘  └─────────────┘
                                     │
                                     v
                            ┌──────────────────┐
                            │ Admin Updates    │
                            │ Progress         │
                            │──────────────────│
                            │From: Belum Dimulai
                            │To:   Dalam Proses
                            │Add: Description  │
                            │Add: Evidence     │
                            │Send notif        │
                            └────────┬─────────┘
                                     │
                                     v
                            ┌──────────────────┐
                            │ Admin Finalizes  │
                            │──────────────────│
                            │progress_status   │
                            │= Selesai         │
                            │Send notif        │
                            └────────┬─────────┘
                                     │
                    ┌────────────────┴────────────────┐
                    │                                 │
                    v                                 v
        ┌──────────────────────┐         ┌────────────────────────┐
        │ Student Gets Notif   │         │ Aspirasi in admin list │
        │ "Aspirasi Selesai"   │         │ PROGRESS: Selesai      │
        │                      │         │────────────────────────│
        │ Opens Aspirasiku     │         │ No more updates        │
        │ Sees Detail Thread   │         │ Admin can export data  │
        │ + RATING FORM        │         └────────────────────────┘
        │                      │
        │ Fills:              │
        │ - Rating 1-5        │
        │ - Feedback text     │
        │ - Submits           │
        └────────┬────────────┘
                 │
                 v
        ┌──────────────────────┐
        │ Rating Saved         │
        │ input_aspirations.   │
        │ rating + feedback    │
        │ updated              │
        │                      │
        │ Thread shows rating  │
        │ Student satisfied ✓  │
        └────────┬─────────────┘
                 │
                 v
        ┌──────────────────────┐
        │ Optional: Student    │
        │ votes on other temp  │
        │ aspirations in Global│
        │ Dashboard            │
        │ (custom aspirations  │
        │  NOT in dashboard)   │
        └────────┬─────────────┘
                 │
                 v
            ┌─────────┐
            │   END   │
            └─────────┘
```

---

## Detailed Activity Flows

### Flow 1: Student Submit Aspirasi (Template)

```
START
  │
  ├─► [Aspirasiku Workspace]
  │   ├─► Click "New Chat"
  │   ├─► Choose "Template" mode ✓
  │   ├─► Step 1: Input Title
  │   ├─► Step 2: Select Category (dropdown)
  │   ├─► Step 3: Select Location (dropdown)
  │   │   └─► If type="kelas" → Input room number required
  │   ├─► Step 4: Write Description
  │   ├─► Step 5: Upload Photo (optional)
  │   ├─► Review & Submit
  │   │
  │   └─► [Backend: InputAspirationController.store()]
  │       ├─► Validate all required fields
  │       ├─► Create InputAspirations record
  │       │   └─► submission_mode = 'template'
  │       │       status = 'menunggu'
  │       │       input_at = now()
  │       ├─► Save image file
  │       ├─► Trigger AspirationApprovedNotification (event)
  │       └─► Redirect to Aspirasiku with success
  │
  └─► [Student sees aspirasi in history with "menunggu" badge]

END
```

### Flow 2: Student Submit Aspirasi (Custom)

```
START
  │
  ├─► [Aspirasiku Workspace]
  │   ├─► Click "New Chat"
  │   ├─► Choose "Custom" mode ✓
  │   ├─► Step 1: Input Title
  │   ├─► Step 2: Input Custom Location (free text)
  │   ├─► Step 3: Explain Priority (custom_context)
  │   ├─► Step 4: Write Description
  │   ├─► Step 5: Upload Photo (optional)
  │   ├─► Review & Submit
  │   │
  │   └─► [Backend: InputAspirationController.store()]
  │       ├─► Validate all required fields
  │       ├─► Create InputAspirations record
  │       │   └─► submission_mode = 'custom'
  │       │       status = 'menunggu'
  │       │       input_at = now()
  │       │       custom_location = user input
  │       │       custom_context = user explanation
  │       │       id_category = default
  │       │       id_location = default
  │       ├─► Save image file
  │       ├─► Trigger event (notification)
  │       └─► Redirect to Aspirasiku
  │
  └─► [Aspirasi queued by input_at]
      └─► Admin sees ANTREAN #X based on time

END
```

### Flow 3: Admin Approve Aspirasi

```
START
  │
  ├─► [Admin: Aspirasi Siswa List]
  │   ├─► View pending aspirations (status = menunggu)
  │   ├─► Filter by mode if needed
  │   ├─► See custom ANTREAN rank
  │   ├─► Click "Detail" on aspirasi
  │   │
  │   └─► [Detail View]
  │       ├─► View full aspirasi content
  │       ├─► View student info
  │       ├─► Fill optional "Admin Message"
  │       ├─► Click "Approve" button
  │       │
  │       └─► [Backend: InputAspirationController.approve()]
  │           ├─► Validate status = 'menunggu'
  │           ├─► Update InputAspirations
  │           │   └─► status = 'diterima'
  │           │       admin_message = input (optional)
  │           ├─► Create Aspirations record
  │           │   ├─► id_input = FK
  │           │   ├─► progress_status = 'Belum Dimulai'
  │           │   ├─► deadline = calculated from SLA
  │           │   └─► priority_level = votes initially 0
  │           ├─► Trigger AspirationApproved event
  │           │   └─► Send notification to student
  │           └─► Redirect to list with success
  │
  └─► [Student receives notification]
      └─► Aspirasi now shows "diterima" in history
          └─► Can see admin message in thread

END
```

### Flow 4: Admin Update Progress

```
START
  │
  ├─► [Admin: Aspirasi Detail - After Approved]
  │   ├─► Current progress: "Belum Dimulai"
  │   ├─► Click "Update Progress" or "Edit"
  │   │
  │   └─► [Progress Update Form]
  │       ├─► Select new status:
  │       │   ├─ Belum Dimulai
  │       │   ├─ Dalam Proses ✓
  │       │   └─ Selesai
  │       ├─► Input description (e.g., "Sudah dimulai perbaikan")
  │       ├─► Upload evidence image (photo of work)
  │       ├─► Submit
  │       │
  │       └─► [Backend: AspirationProgressController.update()]
  │           ├─► Validate input
  │           ├─► Update Aspirations record
  │           │   └─► progress_status = selected
  │           │       progress_description = input
  │           │       progress_evidence_image = saved file
  │           ├─► Create Feedback record (optionally)
  │           ├─► Trigger AspirationProgressUpdated event
  │           │   └─► Send notification to student
  │           └─► Redirect with success
  │
  ├─► [More updates possible until Selesai]
  │
  └─► [Final update: status = Selesai]
      └─► Trigger notification
          └─► Student can now rate

END
```

### Flow 5: Student Rate Aspirasi (Post-Completion)

```
START
  │
  ├─► [Student: Aspirasiku - Detail Thread View]
  │   ├─► Aspirasi status = "diterima"
  │   ├─► Progress = "Selesai"
  │   ├─► RATING FORM appears
  │   │
  │   └─► [Rating Form]
  │       ├─► Select star rating (1-5)
  │       ├─► Optional: Write feedback comment
  │       ├─► Click "Kirim Rating"
  │       │
  │       └─► [Backend: InputAspirationController.rate()]
  │           ├─► Validate:
  │           │   ├─ status = 'diterima'
  │           │   ├─ progress_status = 'Selesai'
  │           │   └─ rating 1-5 required
  │           ├─► Update InputAspirations
  │           │   ├─► rating = value
  │           │   └─► feedback = comment (optional)
  │           ├─► Send success message
  │           └─► Reload thread
  │
  └─► [Rating saved and displayed in thread]

END
```

### Flow 6: Student Vote on Aspirasi (Global Dashboard)

```
START
  │
  ├─► [Student: Global Dashboard]
  │   ├─► View approved template aspirations
  │   ├─► See vote count badge
  │   ├─► Click vote button (thumbs up) ✓
  │   │
  │   └─► [Backend: AspirationInteractionController.toggleVote()]
  │       ├─► Check if already voted (toggle)
  │       ├─► If not voted:
  │       │   └─► Create Votes record (FK aspirations)
  │       ├─► If already voted:
  │       │   └─► Delete Votes record (unlike)
  │       └─► Return vote count
  │
  └─► [Vote count updates in real-time]
      ├─► ≥10 votes → "HIGH PRIORITY" red badge
      ├─ ≥5 votes → "NEED ATTENTION" yellow badge
      └─► Admin sees in list and can sort by votes

END
```

---

## Key Decision Points

| Decision | Template | Custom |
|----------|----------|--------|
| **Store category** | From system list | Auto-assigned default |
| **Store location** | From system list + room | Free text (custom_location) |
| **Track SLA** | Yes (deadline in Aspirations) | No (custom priority only) |
| **Show in dashboard** | Yes (if approved) | No (any status) |
| **Priority method** | Vote count | FIFO (input_at) |
| **Admin view** | Mixed list | Separate ANTREAN view |
| **Rating allowed** | After Selesai | After Selesai |

