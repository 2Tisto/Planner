# Graph Report - Planner  (2026-11-19)

## Corpus Check
- 138 files · ~24,287 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 529 nodes · 856 edges · 80 communities (67 shown, 13 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 11 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `1516411e`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- composer.json
- scripts
- PlanningBoard.php
- devDependencies
- EventAttributes
- DatabaseSeeder.php
- User
- EventException
- Illuminate\View\Component
- README.md
- Event
- ReadPhrase
- PlanningBoard
- Pest.php
- Laravel Application
- Logout.php
- Laravel Application
- profile.blade.php
- verify-email.blade.php
- php
- layout.navigation
- layout/navigation.blade.php
- welcome.blade.php
- SetUserLocale.php
- planning-board.blade.php
- category.category-manager
- delete({{ $category->id }})
- planning.planning-board
- AppServiceProvider.php
- VerifyEmailController.php
- LoginForm

## God Nodes (most connected - your core abstractions)
1. `Event` - 61 edges
2. `User` - 56 edges
3. `PlanningBoard` - 35 edges
4. `Category` - 24 edges
5. `EventException` - 23 edges
6. `OccurrenceMoment` - 13 edges
7. `CategoryManager` - 13 edges
8. `ExpandWeek` - 12 edges
9. `require-dev` - 11 edges
10. `ChangeSeries` - 10 edges

## Surprising Connections (you probably didn't know these)
- `ChangeOccurrence` --references--> `OccurrenceMoment`  [EXTRACTED]
  app/Actions/Event/ChangeOccurrence.php → app/Actions/Event/OccurrenceMoment.php
- `ChangeOccurrence` --references--> `SnapTime`  [EXTRACTED]
  app/Actions/Event/ChangeOccurrence.php → app/Actions/Event/SnapTime.php
- `ChangeSeries` --references--> `OccurrenceMoment`  [EXTRACTED]
  app/Actions/Event/ChangeSeries.php → app/Actions/Event/OccurrenceMoment.php
- `ChangeSeries` --references--> `SnapTime`  [EXTRACTED]
  app/Actions/Event/ChangeSeries.php → app/Actions/Event/SnapTime.php
- `CreateEvent` --references--> `EventAttributes`  [EXTRACTED]
  app/Actions/Event/CreateEvent.php → app/Actions/Event/EventAttributes.php

## Import Cycles
- None detected.

## Communities (80 total, 13 thin omitted)

### Community 0 - "composer.json"
Cohesion: 0.04
Nodes (45): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+37 more)

### Community 1 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 2 - "PlanningBoard.php"
Cohesion: 0.07
Nodes (11): SeedDefaultCategories, PlaceEventOnDay, CategoryFactory, EventFactory, SubjectFactory, UserFactory, DateTimeInterface, Illuminate\Database\Eloquent\Factories\Factory (+3 more)

### Community 3 - "devDependencies"
Cohesion: 0.07
Nodes (26): autoprefixer, concurrently, @laravel/multiplex, laravel-vite-plugin, devDependencies, autoprefixer, concurrently, laravel-vite-plugin (+18 more)

### Community 4 - "EventAttributes"
Cohesion: 0.17
Nodes (5): CreateEvent, EventAttributes, OverrideOccurrence, UpdateEvent, FindOrCreateSubject

### Community 5 - "DatabaseSeeder.php"
Cohesion: 0.60
Nodes (3): DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 6 - "User"
Cohesion: 0.07
Nodes (15): CreateCategory, DeleteCategory, UpdateCategory, UpdateProfileInformation, CategoryManager, Category, User, CategoryPolicy (+7 more)

### Community 7 - "EventException"
Cohesion: 0.10
Nodes (7): CancelOccurrence, EventException, Carbon, Subject, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo, self

### Community 8 - "Illuminate\View\Component"
Cohesion: 0.43
Nodes (4): AppLayout, GuestLayout, Illuminate\View\Component, Illuminate\View\View

### Community 9 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 10 - "Event"
Cohesion: 0.07
Nodes (15): ChangeOccurrence, Carbon, ChangeSeries, DeleteEvent, DuplicateEvent, ExpandWeek, FindOverlaps, MoveEvent (+7 more)

### Community 14 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 16 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 17 - "profile.blade.php"
Cohesion: 0.50
Nodes (3): profile.delete-user-form, profile.update-password-form, profile.update-profile-information-form

### Community 27 - "SetUserLocale.php"
Cohesion: 0.39
Nodes (4): SetUserLocale, Closure, Illuminate\Http\Request, Symfony\Component\HttpFoundation\Response

### Community 54 - "planning-board.blade.php"
Cohesion: 0.13
Nodes (14): applyPending(, cancelPending, closeComposer, duplicate, goToToday, openCreate(, openEdit({{ $block[, openQuickAdd (+6 more)

### Community 76 - "AppServiceProvider.php"
Cohesion: 0.27
Nodes (3): AppServiceProvider, VoltServiceProvider, Illuminate\Support\ServiceProvider

### Community 78 - "VerifyEmailController.php"
Cohesion: 0.31
Nodes (5): VerifyEmailController, Controller, Illuminate\Contracts\Auth\MustVerifyEmail, Illuminate\Foundation\Auth\EmailVerificationRequest, Illuminate\Http\RedirectResponse

## Knowledge Gaps
- **100 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+95 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **13 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `PlanningBoard.php`, `EventAttributes`, `DatabaseSeeder.php`, `EventException`, `Event`, `PlanningBoard`, `VerifyEmailController.php`?**
  _High betweenness centrality (0.105) - this node is a cross-community bridge._
- **Why does `Event` connect `Event` to `PlanningBoard.php`, `EventAttributes`, `User`, `EventException`, `PlanningBoard`, `AppServiceProvider.php`?**
  _High betweenness centrality (0.088) - this node is a cross-community bridge._
- **Why does `PlanningBoard` connect `PlanningBoard` to `PlanningBoard.php`, `User`?**
  _High betweenness centrality (0.029) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `User` (e.g. with `.definition()` and `.definition()`) actually correct?**
  _`User` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Are the 2 inferred relationships involving `Category` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Category` has 2 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _100 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.043478260869565216 - nodes in this community are weakly interconnected._