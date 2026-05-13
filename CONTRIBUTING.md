# Repository Rules

## Branch Flow

```
feature/  fix/  hotfix/  chore/  release/  claude/
                      ↓  PR
                   staging
                      ↓  PR
                     main
```

Never push directly to `main` or `staging` — all changes go through a pull request.

The only branch allowed to merge into `main` is `staging`.

## Branch Naming

Branches must use one of these prefixes:

| Prefix | Purpose |
|---|---|
| `feature/` | New functionality |
| `fix/` | Bug fixes |
| `hotfix/` | Urgent production fixes |
| `chore/` | Maintenance, dependencies, tooling |
| `release/` | Release preparation |
| `claude/` | AI-assisted changes |

## Pull Request Requirements

Every PR into `staging` or `main` must meet all of the following before it can be merged:

- **1 approval** — at least one review is required; approvals are dismissed when new commits are pushed
- **Status checks pass** — branch name check, source branch check (for `main`), and `composer audit` must all be green
- **Branch up to date** — your branch must be current with the target before merging

`main` and `staging` cannot be force-pushed to or deleted.

## Deployments

Merging into `staging` or `main` automatically triggers a deployment — no manual steps needed.
