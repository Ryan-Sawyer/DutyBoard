# 0003: One install per agency, on the agency's server

**Status:** Accepted · 2026-10-29

## Situation

Agencies need to control where their staff data lives. A single shared, hosted service for many agencies would need tenant separation in every table and every query.

## Decision

Each agency runs **its own install**: its own containers and its own database. The code doesn't try to serve several agencies from one database.

## Why

- Each agency's data is separated by design, with no tenant logic to get wrong.
- It matches the requirement that the app runs on a server the agency controls.
- A hosted offering can still run one install per customer.

## What follows

- Installation and upgrades must be simple enough for an agency's IT person: one compose file, documented steps, and settings in `.env`.
- Agency-specific settings, such as time zone, pay period start, the consecutive-hours limit, and rotation patterns, live in the database and are editable by an administrator, not in code.
- Stations and battalions are modeled from the start, so one install can serve a large multi-station department.
