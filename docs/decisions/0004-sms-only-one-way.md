# 0004: Notifications by one-way SMS only

**Status:** Accepted · 2026-10-29

## Situation

Supervisors need to reach off-duty staff quickly for coverage, and crews need to hear when a request is approved or a schedule is published. Email setup, deliverability, and maintenance would add work for every agency, and crews read texts faster than email.

## Decision

Send notifications as **one-way text messages**. Each text says what's needed and links to DutyBoard, where the person signs in and responds. Email and push notifications aren't supported. Password reset uses an administrator or a texted code.

## Why

- At a small agency's volume, texting costs a few dollars a month.
- Responding in the app keeps approvals, the consecutive-hours check, and the audit trail in one place, instead of parsing text replies.

## What follows

- Every account needs a mobile number and a recorded consent to receive texts. Staff can turn texts off.
- Texts must fit in one message (160 characters, no emoji) and link to the agency's own domain, never a public link shortener.
- STOP opt-outs must be honored and recorded.
- US carriers require A2P 10DLC registration before sending. The install guide covers it.
- All sending goes through one interface, so another provider can be added with one class.
