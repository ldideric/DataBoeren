# Repository Rules

This document describes the branching and merge rules enforced by the GitHub Actions workflows in this repository.

## Branch Flow

All changes must flow through the following pipeline:

```
feature/... (or fix/, hotfix/, chore/, release/)
       ↓  PR
    staging
       ↓  PR
      main
```

**Direct pushes to `main` or `staging` are not allowed.** Every change must arrive via a pull request.

## Branch Naming

Every branch opened in a PR against `main` or `staging` must follow one of these prefixes:

| Prefix | Purpose |
|---|---|
| `feature/` | New functionality |
| `fix/` | Bug fixes |
| `hotfix/` | Urgent production fixes |
| `chore/` | Maintenance, dependencies, tooling |
| `release/` | Release preparation |

A branch named exactly `staging` is also allowed (it is the only branch permitted to merge into `main`).

Examples of valid branch names:
- `feature/reservation-form`
- `fix/payment-calculation`
- `hotfix/login-crash`
- `chore/update-dependencies`
- `release/1.2.0`

## Merging to `main`

Pull requests targeting `main` **must come from `staging`**. PRs from any other branch are rejected automatically. This ensures that every change has passed through the staging environment before reaching production.

## Deployments

Merges to `staging` and `main` automatically trigger a deployment:

- `staging` → triggers the `deploy-staging` event
- `main` → triggers the `deploy-de-groene-weide` event

No manual deployment steps are needed after a merge.

## Security Audit

Every PR targeting `main` or `staging` runs `composer audit` to check for known vulnerabilities in PHP dependencies. A PR cannot be merged if the audit reports any issues.

## Summary

1. Create a branch with the correct prefix (`feature/`, `fix/`, `hotfix/`, `chore/`, or `release/`).
2. Open a PR from your branch into `staging`.
3. After review and merge, open a PR from `staging` into `main`.
4. Deployment happens automatically on merge.
