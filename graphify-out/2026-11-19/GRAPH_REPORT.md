# Graph Report - Planner  (2026-11-19)

## Corpus Check
- 133 files · ~22,452 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 507 nodes · 807 edges · 84 communities (68 shown, 16 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 11 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `3e53f368`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- composer.json
- scripts
- User.php
- devDependencies
- User
- AppServiceProvider
- Category
- VerifyEmailController.php
- Illuminate\View\Component
- README.md
- Event
- EventException
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
- PlaceEventOnDay.php
- Illuminate\Support\Carbon
- SnapTime
- PlanningBoard.php
- Illuminate\Database\Eloquent\Relations\BelongsTo
- ChangeOccurrence
- OccurrenceMoment

## God Nodes (most connected - your core abstractions)
1. `Event` - 61 edges
2. `User` - 56 edges
3. `PlanningBoard` - 30 edges
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
- `ExpandWeek` --references--> `OccurrenceMoment`  [EXTRACTED]
  app/Actions/Event/ExpandWeek.php → app/Actions/Event/OccurrenceMoment.php

## Import Cycles
- None detected.

## Communities (84 total, 16 thin omitted)

### Community 0 - "composer.json"
Cohesion: 0.04
Nodes (45): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+37 more)

### Community 1 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 2 - "User.php"
Cohesion: 0.09
Nodes (10): CategoryFactory, EventFactory, SubjectFactory, UserFactory, DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Database\Seeder (+2 more)

### Community 3 - "devDependencies"
Cohesion: 0.07
Nodes (26): autoprefixer, concurrently, @laravel/multiplex, laravel-vite-plugin, devDependencies, autoprefixer, concurrently, laravel-vite-plugin (+18 more)

### Community 4 - "User"
Cohesion: 0.06
Nodes (15): SeedDefaultCategories, CreateEvent, EventAttributes, OverrideOccurrence, UpdateEvent, UpdateProfileInformation, FindOrCreateSubject, Subject (+7 more)

### Community 5 - "AppServiceProvider"
Cohesion: 0.27
Nodes (3): AppServiceProvider, VoltServiceProvider, Illuminate\Support\ServiceProvider

### Community 6 - "Category"
Cohesion: 0.17
Nodes (8): CreateCategory, DeleteCategory, UpdateCategory, CategoryManager, Category, Illuminate\Contracts\View\View, Illuminate\Database\Eloquent\Collection, Livewire\Component

### Community 7 - "VerifyEmailController.php"
Cohesion: 0.18
Nodes (7): VerifyEmailController, Controller, LoginForm, Illuminate\Contracts\Auth\MustVerifyEmail, Illuminate\Foundation\Auth\EmailVerificationRequest, Illuminate\Http\RedirectResponse, Livewire\Form

### Community 8 - "Illuminate\View\Component"
Cohesion: 0.43
Nodes (4): AppLayout, GuestLayout, Illuminate\View\Component, Illuminate\View\View

### Community 9 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 10 - "Event"
Cohesion: 0.19
Nodes (4): DeleteEvent, DuplicateEvent, Event, Carbon

### Community 11 - "EventException"
Cohesion: 0.17
Nodes (4): ChangeSeries, EventException, Carbon, self

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
Cohesion: 0.17
Nodes (11): applyPending(, cancelPending, closeComposer, duplicate, goToToday, openCreate(, openQuickAdd, remove (+3 more)

### Community 78 - "Illuminate\Support\Carbon"
Cohesion: 0.26
Nodes (4): ExpandWeek, FindOverlaps, Occurrence, Illuminate\Support\Carbon

### Community 79 - "SnapTime"
Cohesion: 0.20
Nodes (3): MoveEvent, ResizeEvent, SnapTime

## Knowledge Gaps
- **97 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+92 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **16 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `User.php`, `Category`, `VerifyEmailController.php`, `PlanningBoard`, `Illuminate\Support\Carbon`, `PlanningBoard.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`?**
  _High betweenness centrality (0.104) - this node is a cross-community bridge._
- **Why does `Event` connect `Event` to `User`, `AppServiceProvider`, `VerifyEmailController.php`, `EventException`, `PlanningBoard`, `Illuminate\Support\Carbon`, `SnapTime`, `PlanningBoard.php`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `ChangeOccurrence`, `OccurrenceMoment`?**
  _High betweenness centrality (0.089) - this node is a cross-community bridge._
- **Why does `PlanningBoard` connect `PlanningBoard` to `PlanningBoard.php`, `Category`?**
  _High betweenness centrality (0.023) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `User` (e.g. with `.definition()` and `.definition()`) actually correct?**
  _`User` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Are the 2 inferred relationships involving `Category` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Category` has 2 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _97 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.043478260869565216 - nodes in this community are weakly interconnected._