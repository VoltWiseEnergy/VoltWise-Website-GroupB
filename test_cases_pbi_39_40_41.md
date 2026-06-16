# Test Cases – PBI-39, PBI-40, PBI-41
## VoltWise Energy – Gamification Feature

---

## PBI-39: Earn Points

**User Story:**
As a user, I want to earn points for saving energy, staying under budget, and maintaining consistent usage, so that I feel motivated to use energy wisely.

**Relevant Files:**
- `app/Services/PointService.php` – Logic awarding `consistent_logging` (+5 pts), `under_budget` (+50 pts), `low_usage` (+10 pts), `very_low_usage` (+20 pts)
- `app/Models/UserPointLog.php` – Stores each point event per user per date
- `app/Http/Controllers/PointsController.php` – Renders `/points` page with total points & history
- `resources/views/points/index.blade.php` – Front-end display of total points, level, and history

---

### TC-39-01 (Positive) – User Earns Points for Logging Usage Under Budget

| Field               | Detail |
|---------------------|--------|
| **PBI ID**          | PBI-39 |
| **Case ID**         | TC-39-01 |
| **Test Scenario**   | User receives points after logging device usage and monthly cost is still under budget |
| **Type**            | Positive |
| **Test Case**       | Verify that the system successfully awards `consistent_logging` (+5 pts) and `under_budget` (+50 pts) points when the conditions are met |
| **Pre Condition**   | User is logged in. User has a monthly budget set (e.g. Rp 500,000). Total monthly device cost is below the budget. User has not logged any usage today. User is on the Daily Tracker page. |

**Steps:**

| No. | Steps Description | Expected Result |
|-----|-------------------|-----------------|
| 1   | Log in to the application with a valid user account | Successfully redirected to the Dashboard page |
| 2   | Navigate to **Daily Tracker** (`/usage/tracker`) | Daily Tracker page loads with the list of devices |
| 3   | Select a device (e.g. Electric Fan) and click **Log Usage** / fill in today's usage data | A success message appears confirming the usage data has been saved |
| 4   | Navigate to the **My Points** page (`/points`) | My Points page loads successfully |
| 5   | Check the **Points History** section for today's date | Entries **"Logged Usage (+5 pts)"** and **"Stayed Under Budget (+50 pts)"** appear for today |
| 6   | Check the **Total Points** value in the hero section | Total points have increased accordingly (e.g. from 0 to 55 pts) |
| 7   | Refresh the My Points page and check the Points History again | The number of point entries for today remains the same — no duplicates |

**Expected Result:** User's total points increase by 55 pts (5 + 50). History shows two events for today. No duplicate events appear after refreshing the page.

---

### TC-39-02 (Negative) – User Does Not Receive Duplicate Points for the Same Event on the Same Day

| Field               | Detail |
|---------------------|--------|
| **PBI ID**          | PBI-39 |
| **Case ID**         | TC-39-02 |
| **Test Scenario**   | System prevents duplicate points from being awarded for the same event on the same date |
| **Type**            | Negative |
| **Test Case**       | Verify that `consistent_logging` points are only awarded once per day, even if the user logs usage more than once on the same day |
| **Pre Condition**   | User is logged in. User has already logged usage today and received a `consistent_logging` point — visible in the Points History on the My Points page. User is on the Daily Tracker page. |

**Steps:**

| No. | Steps Description | Expected Result |
|-----|-------------------|-----------------|
| 1   | Log in to the application | Successfully redirected to the Dashboard |
| 2   | Navigate to the **My Points** page (`/points`) and note the current **Total Points** | Current total points are recorded (e.g. 55 pts) |
| 3   | Navigate to **Daily Tracker** and log usage for a different device on the same day | A success message appears confirming the usage data has been saved |
| 4   | Navigate back to the **My Points** page (`/points`) | My Points page loads successfully |
| 5   | Check the **Points History** for today's date | There is only **one** "Logged Usage (+5 pts)" entry — no duplicate entries for the same date |
| 6   | Check the **Total Points** value in the hero section | Total points are **unchanged** from the value noted in step 2 (still 55 pts) |

**Expected Result:** The system records only one `consistent_logging` event per user per day. No duplicate points are added. Total points do not change after the second log.

---
---

## PBI-40: Unlock Badges / Achievements

**User Story:**
As a user, I want to unlock badges when I achieve milestones (e.g. reduced usage, consistent savings), so that I feel rewarded for my progress.

**Relevant Files:**
- `app/Services/BadgeService.php` – Logic for checking and awarding badges based on streak, savings, usage, and point milestones
- `app/Models/Badge.php` – Badge definitions (key, name, description, emoji, category)
- `app/Models/UserBadge.php` – User-badge relationship (pivot table `user_badges`)
- `resources/views/points/index.blade.php` – Badge grid display (unlocked/locked), badge toast notification

---

### TC-40-01 (Positive) – User Unlocks the "First Log" Badge After Logging Usage for the First Time

| Field               | Detail |
|---------------------|--------|
| **PBI ID**          | PBI-40 |
| **Case ID**         | TC-40-01 |
| **Test Scenario**   | User automatically receives the `first_log` badge after successfully logging device usage for the first time |
| **Type**            | Positive |
| **Test Case**       | Verify that the `first_log` badge appears as unlocked on the My Points page and a toast notification is shown after the user logs usage for the first time |
| **Pre Condition**   | User is logged in. User has never logged any device usage before. All badges on the My Points page are displayed as locked (grayscale). User is on the Daily Tracker page. |

**Steps:**

| No. | Steps Description | Expected Result |
|-----|-------------------|-----------------|
| 1   | Log in to the application with a new account that has never logged any usage | Successfully redirected to the Dashboard |
| 2   | Navigate to **Daily Tracker** (`/usage/tracker`) | Daily Tracker page loads |
| 3   | Log usage for at least one device for today | A success message appears confirming the data has been saved |
| 4   | Navigate to the **My Points** page (`/points`) | My Points page loads successfully |
| 5   | Check the **My Badges** section | A **toast notification** appears on screen with the text "Badge Unlocked! [First Log badge name]" |
| 6   | Check the badge grid under the **Streak** category | The **"First Log"** badge is displayed as **unlocked** — full color, with a green checkmark icon, not grayscale |
| 7   | Check the date detail inside the "First Log" badge card | The earned date shows **today's date** |
| 8   | Wait a few seconds and observe the toast notification | The toast disappears automatically after approximately 3 seconds |

**Expected Result:** The `first_log` badge is successfully unlocked, displayed with a visual style different from locked badges, and a toast notification briefly appears to inform the user.

---

### TC-40-02 (Negative) – Badge Is Not Awarded Twice for the Same Condition

| Field               | Detail |
|---------------------|--------|
| **PBI ID**          | PBI-40 |
| **Case ID**         | TC-40-02 |
| **Test Scenario**   | System prevents a duplicate badge from being awarded to a user who already owns that badge |
| **Type**            | Negative |
| **Test Case**       | Verify that the `first_log` badge is not re-awarded and the badge toast does not appear again when the user logs usage on the following day |
| **Pre Condition**   | User is logged in. User already owns the "First Log" badge — it is displayed as unlocked on the My Points page. User is on the Daily Tracker page. |

**Steps:**

| No. | Steps Description | Expected Result |
|-----|-------------------|-----------------|
| 1   | Log in to the application | Successfully redirected to the Dashboard |
| 2   | Navigate to the **My Points** page (`/points`) and note the current number of unlocked badges | The "First Log" badge is visible as unlocked; total number of unlocked badges is noted |
| 3   | Navigate to **Daily Tracker** and log device usage (e.g. on the 2nd consecutive day) | A success message appears confirming the data has been saved |
| 4   | Navigate back to the **My Points** page (`/points`) | My Points page loads successfully |
| 5   | Check whether a toast notification for the "First Log" badge appears | **No** toast notification for the "First Log" badge appears (badge already owned) |
| 6   | Check the badge grid in the **My Badges** section | The "First Log" badge is still displayed **once** in unlocked status — no visual duplicates |
| 7   | Compare the total number of unlocked badges with the value noted in step 2 | The number of unlocked badges **has not increased** as a result of this second log |

**Expected Result:** The system does not award a duplicate badge. The badge toast does not appear for a badge already owned. The number of badges on the My Points page remains consistent.

---
---

## PBI-41: Display Leaderboard

**User Story:**
As a user, I want to see a leaderboard ranking users by points and performance, so that I can compare my progress with others.

**Relevant Files:**
- `app/Http/Controllers/LeaderboardController.php` – Queries all users sorted by `points` (DESC), resolves levels, determines rank, and identifies the current user ("You" tag)
- `resources/views/leaderboard/index.blade.php` – Displays podium for top-3, current user rank banner, all rankings table, and level badge per user

---

### TC-41-01 (Positive) – Leaderboard Displays Users Sorted by Highest Points

| Field               | Detail |
|---------------------|--------|
| **PBI ID**          | PBI-41 |
| **Case ID**         | TC-41-01 |
| **Test Scenario**   | The leaderboard displays all registered users sorted from highest to lowest total points, with the currently logged-in user highlighted |
| **Type**            | Positive |
| **Test Case**       | Verify that the leaderboard loads the top-3 podium, the current user's rank banner, and a correctly sorted full rankings table |
| **Pre Condition**   | At least 3 users are registered with different point totals (e.g. User A = 700 pts, User B = 300 pts, User C = 100 pts). User is logged in as User B. User is on the Dashboard page. |

**Steps:**

| No. | Steps Description | Expected Result |
|-----|-------------------|-----------------|
| 1   | Log in to the application as User B (300 pts) | Successfully redirected to the Dashboard |
| 2   | Navigate to the **Leaderboard** page (`/leaderboard`) | Leaderboard page loads successfully |
| 3   | Check the **Podium** section (top 3) | Podium displays User A in 1st place (🥇), User B in 2nd place (🥈), and User C in 3rd place (🥉) — sorted correctly by points |
| 4   | Check the **"Your Rank"** banner above the table | Banner shows "#2" as User B's rank, along with total points (300 pts) and the corresponding level badge |
| 5   | Check the **All Rankings** table | Table lists all users sorted from rank 1 downward (User A → User B → User C) |
| 6   | Check User B's row in the table | User B's row has a distinct highlight style and a **"You"** label tag |
| 7   | Check the **level badge** column for each row | Each user's level badge matches their total points (Bronze: 0–99, Silver: 100–499, Gold: 500–699, Platinum: 700+) |
| 8   | Check the **badge count** column for each row | The badge count shown for each user is accurate and consistent with their unlocked badges visible on their profile |

**Expected Result:** The leaderboard displays complete and correctly sorted data. User B is marked with a "You" tag. The podium shows the correct top-3 order. Each user's level badge matches their point total.

---

### TC-41-02 (Negative) – User with Fewer Points is Not Ranked Above Users with More Points

| Field               | Detail |
|---------------------|--------|
| **PBI ID**          | PBI-41 |
| **Case ID**         | TC-41-02 |
| **Test Scenario**   | The leaderboard does not display a lower-point user in a higher rank position than users with more points |
| **Type**            | Negative |
| **Test Case**       | Verify that a user with the lowest points (User C) does not appear above users with higher points (User A and User B) anywhere on the leaderboard page |
| **Pre Condition**   | At least 3 users are registered with different point totals (e.g. User A = 600 pts, User B = 100 pts, User C = 0 pts). User is logged in as User C. User is on the Dashboard page. |

**Steps:**

| No. | Steps Description | Expected Result |
|-----|-------------------|-----------------|
| 1   | Log in to the application as User C (0 pts — the lowest ranked user) | Successfully redirected to the Dashboard |
| 2   | Navigate to the **Leaderboard** page (`/leaderboard`) | Leaderboard page loads successfully |
| 3   | Check the **Podium** section (top 3) | User C is **not** placed in 1st or 2nd position on the podium — User A (🥇) and User B (🥈) occupy the top positions |
| 4   | Check the **"Your Rank"** banner | Banner shows **"#3"** as User C's rank — not #1 or #2 |
| 5   | Check the **All Rankings** table and locate User C's row | User C's row appears **below** User A's and User B's rows in the table |
| 6   | Scroll through the table from top to bottom | User A appears first (rank 1), followed by User B (rank 2), and User C appears last (rank 3) — no lower-point user appears above a higher-point user |

**Expected Result:** User C (0 pts) is displayed at rank #3, below User A and User B. The leaderboard does not incorrectly place a lower-point user in a higher position. The ranking order is strictly descending by total points.

---

## Test Cases Summary

| PBI ID | Case ID  | Type     | Test Scenario |
|--------|----------|----------|---------------|
| PBI-39 | TC-39-01 | Positive | User receives points after logging usage and staying under budget |
| PBI-39 | TC-39-02 | Negative | System prevents duplicate points for the same event on the same day |
| PBI-40 | TC-40-01 | Positive | User unlocks the "First Log" badge after logging usage for the first time |
| PBI-40 | TC-40-02 | Negative | Badge is not awarded twice for a condition already fulfilled |
| PBI-41 | TC-41-01 | Positive | Leaderboard displays users sorted by highest points |
| PBI-41 | TC-41-02 | Negative | User with fewer points is not ranked above users with more points |
