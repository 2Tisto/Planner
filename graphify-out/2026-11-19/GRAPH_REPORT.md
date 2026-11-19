# Graph Report - Planner  (2026-11-19)

## Corpus Check
- 117 files · ~18,216 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 416 nodes · 569 edges · 76 communities (64 shown, 12 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 9 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `d91aa27e`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- composer.json
- scripts
- User.php
- devDependencies
- User
- AppServiceProvider
- CategoryManager
- VerifyEmailController.php
- Illuminate\View\Component
- README.md
- Event
- LoginForm
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

## God Nodes (most connected - your core abstractions)
1. `User` - 50 edges
2. `Event` - 29 edges
3. `Category` - 24 edges
4. `PlanningBoard` - 21 edges
5. `CategoryManager` - 13 edges
6. `require-dev` - 11 edges
7. `Subject` - 9 edges
8. `scripts` - 9 edges
9. `EventAttributes` - 8 edges
10. `setup` - 7 edges

## Surprising Connections (you probably didn't know these)
- `CreateEvent` --references--> `EventAttributes`  [EXTRACTED]
  app/Actions/Event/CreateEvent.php → app/Actions/Event/EventAttributes.php
- `EventAttributes` --references--> `FindOrCreateSubject`  [EXTRACTED]
  app/Actions/Event/EventAttributes.php → app/Actions/Subject/FindOrCreateSubject.php
- `UpdateEvent` --references--> `EventAttributes`  [EXTRACTED]
  app/Actions/Event/UpdateEvent.php → app/Actions/Event/EventAttributes.php
- `VerifyEmailController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Auth/VerifyEmailController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (76 total, 12 thin omitted)

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
Cohesion: 0.07
Nodes (13): CreateCategory, SeedDefaultCategories, CreateEvent, EventAttributes, UpdateEvent, UpdateProfileInformation, FindOrCreateSubject, Category (+5 more)

### Community 5 - "AppServiceProvider"
Cohesion: 0.27
Nodes (3): AppServiceProvider, VoltServiceProvider, Illuminate\Support\ServiceProvider

### Community 6 - "CategoryManager"
Cohesion: 0.12
Nodes (8): DeleteCategory, UpdateCategory, PlaceEventOnDay, CategoryManager, DateTimeInterface, Illuminate\Contracts\View\View, Illuminate\Database\Eloquent\Collection, Livewire\Component

### Community 7 - "VerifyEmailController.php"
Cohesion: 0.31
Nodes (5): VerifyEmailController, Controller, Illuminate\Contracts\Auth\MustVerifyEmail, Illuminate\Foundation\Auth\EmailVerificationRequest, Illuminate\Http\RedirectResponse

### Community 8 - "Illuminate\View\Component"
Cohesion: 0.43
Nodes (4): AppLayout, GuestLayout, Illuminate\View\Component, Illuminate\View\View

### Community 9 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 10 - "Event"
Cohesion: 0.13
Nodes (9): DeleteEvent, Event, Carbon, Subject, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo (+1 more)

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
Cohesion: 0.20
Nodes (9): closeComposer, goToToday, openCreate(, openEdit({{ $block[, openQuickAdd, remove, selectDate(, $set( (+1 more)

## Knowledge Gaps
- **95 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+90 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **12 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `User.php`, `CategoryManager`, `VerifyEmailController.php`, `Event`, `PlanningBoard`?**
  _High betweenness centrality (0.095) - this node is a cross-community bridge._
- **Why does `Event` connect `Event` to `User`, `AppServiceProvider`, `CategoryManager`, `VerifyEmailController.php`, `LoginForm`, `PlanningBoard`?**
  _High betweenness centrality (0.048) - this node is a cross-community bridge._
- **Why does `Category` connect `User` to `Event`, `CategoryManager`?**
  _High betweenness centrality (0.022) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `User` (e.g. with `.definition()` and `.definition()`) actually correct?**
  _`User` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Are the 3 inferred relationships involving `Event` (e.g. with `.__invoke()` and `.ensureIsNotRateLimited()`) actually correct?**
  _`Event` has 3 INFERRED edges - model-reasoned connections that need verification._
- **Are the 2 inferred relationships involving `Category` (e.g. with `.__invoke()` and `.__invoke()`) actually correct?**
  _`Category` has 2 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _95 weakly-connected nodes found - possible documentation gaps or missing edges._