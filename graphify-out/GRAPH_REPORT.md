# Graph Report - Planner  (2026-11-20)

## Corpus Check
- 218 files · ~34,201 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 863 nodes · 1653 edges · 112 communities (81 shown, 31 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 28 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `e7f94652`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- composer.json
- scripts
- User.php
- devDependencies
- EventException
- Event
- User
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Illuminate\View\Component
- README.md
- Illuminate\Support\Carbon
- ReadPhrase
- PlanningBoard
- Pest.php
- Laravel Application
- Logout.php
- Laravel Application
- profile.blade.php
- verify-email.blade.php
- php
- app.blade.php
- layout/navigation.blade.php
- welcome.blade.php
- SetUserLocale.php
- Illuminate\Database\Eloquent\Model
- planning-board.blade.php
- guest.blade.php
- Subject
- category.category-manager
- delete({{ $category->id }})
- planning.planning-board
- Task
- VerifyEmailController.php
- Illuminate\Database\Eloquent\Builder
- Deadline
- Habit
- Illuminate\Database\Eloquent\Factories\Factory
- SnapTime
- Subtask
- DueReminder.php
- SendDueReminders
- Task.php
- task-board.blade.php
- ChangeSeries
- OccurrenceMoment
- EventException.php
- ChangeOccurrence
- revision-board.blade.php
- Habit.php
- subject-manager.blade.php
- home-board.blade.php
- habit-board.blade.php
- deadline.deadline-board
- habit.habit-board
- home.home-board
- remove({{ $row[
- review.blade.php
- revisions.blade.php
- share.blade.php
- subjects.blade.php
- tasks.blade.php

## God Nodes (most connected - your core abstractions)
1. `User` - 127 edges
2. `Event` - 62 edges
3. `PlanningBoard` - 37 edges
4. `Task` - 33 edges
5. `ExpandWeek` - 26 edges
6. `Category` - 24 edges
7. `Subject` - 24 edges
8. `EventException` - 23 edges
9. `Deadline` - 20 edges
10. `RevisionItem` - 18 edges

## Surprising Connections (you probably didn't know these)
- `create()` --references--> `User`  [EXTRACTED]
  app/Policies/ChecksOwner.php → app/Models/User.php
- `viewAny()` --references--> `User`  [EXTRACTED]
  app/Policies/ChecksOwner.php → app/Models/User.php
- `ChangeOccurrence` --references--> `OccurrenceMoment`  [EXTRACTED]
  app/Actions/Event/ChangeOccurrence.php → app/Actions/Event/OccurrenceMoment.php
- `ChangeOccurrence` --references--> `SnapTime`  [EXTRACTED]
  app/Actions/Event/ChangeOccurrence.php → app/Actions/Event/SnapTime.php
- `ChangeSeries` --references--> `OccurrenceMoment`  [EXTRACTED]
  app/Actions/Event/ChangeSeries.php → app/Actions/Event/OccurrenceMoment.php

## Import Cycles
- None detected.

## Communities (112 total, 31 thin omitted)

### Community 0 - "composer.json"
Cohesion: 0.04
Nodes (45): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+37 more)

### Community 1 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 2 - "User.php"
Cohesion: 0.07
Nodes (10): PlaceEventOnDay, AppServiceProvider, VoltServiceProvider, EventFactory, DatabaseSeeder, DateTimeInterface, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder (+2 more)

### Community 3 - "devDependencies"
Cohesion: 0.07
Nodes (26): autoprefixer, concurrently, @laravel/multiplex, laravel-vite-plugin, devDependencies, autoprefixer, concurrently, laravel-vite-plugin (+18 more)

### Community 4 - "EventException"
Cohesion: 0.13
Nodes (4): OverrideOccurrence, EventException, Carbon, self

### Community 5 - "Event"
Cohesion: 0.15
Nodes (4): DeleteEvent, Event, Carbon, EventPolicy

### Community 6 - "User"
Cohesion: 0.06
Nodes (16): CreateCategory, DeleteCategory, SeedDefaultCategories, UpdateCategory, CreateEvent, EventAttributes, UpdateEvent, UpdateProfileInformation (+8 more)

### Community 8 - "Illuminate\View\Component"
Cohesion: 0.43
Nodes (4): AppLayout, GuestLayout, Illuminate\View\Component, Illuminate\View\View

### Community 9 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 10 - "Illuminate\Support\Carbon"
Cohesion: 0.08
Nodes (12): DuplicateEvent, ExpandWeek, FindOverlaps, Occurrence, BuildCalendar, DescribeNow, Carbon, SuggestFreeHour (+4 more)

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

### Community 33 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.20
Nodes (5): ReminderSend, create(), owns(), viewAny(), Illuminate\Database\Eloquent\Model

### Community 54 - "planning-board.blade.php"
Cohesion: 0.12
Nodes (15): applyPending(, cancelPending, closeComposer, duplicate, goToToday, openCreate(, openEdit({{ $block[, openEdit({{ $focus[ (+7 more)

### Community 65 - "Subject"
Cohesion: 0.05
Nodes (19): SaveReview, SaveRevision, SaveSubject, next(), self, timezone(), user(), Carbon (+11 more)

### Community 76 - "Task"
Cohesion: 0.06
Nodes (14): OpenShare, RecordStudy, SaveTask, next(), self, HomeBoard, Carbon, TaskBoard (+6 more)

### Community 78 - "VerifyEmailController.php"
Cohesion: 0.18
Nodes (7): VerifyEmailController, Controller, LoginForm, Illuminate\Contracts\Auth\MustVerifyEmail, Illuminate\Foundation\Auth\EmailVerificationRequest, Illuminate\Http\RedirectResponse, Livewire\Form

### Community 79 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.21
Nodes (3): SubjectFactory, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Factories\HasFactory

### Community 80 - "Deadline"
Cohesion: 0.12
Nodes (6): SaveDeadline, DeadlineBoard, Deadline, Carbon, DeadlinePolicy, ChecksOwner

### Community 81 - "Habit"
Cohesion: 0.11
Nodes (6): ToggleHabit, HabitBoard, Carbon, Habit, HabitLog, HabitPolicy

### Community 84 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.15
Nodes (7): CategoryFactory, DeadlineFactory, RevisionItemFactory, TaskFactory, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 85 - "SnapTime"
Cohesion: 0.18
Nodes (3): MoveEvent, ResizeEvent, SnapTime

### Community 86 - "Subtask"
Cohesion: 0.27
Nodes (3): AddSubtask, Subtask, SubtaskPolicy

### Community 87 - "DueReminder.php"
Cohesion: 0.31
Nodes (6): DueReminder, Illuminate\Bus\Queueable, Illuminate\Mail\Mailable, Illuminate\Mail\Mailables\Content, Illuminate\Mail\Mailables\Envelope, Illuminate\Queue\SerializesModels

### Community 88 - "SendDueReminders"
Cohesion: 0.33
Nodes (3): SendDueReminders, SendReminders, Illuminate\Console\Command

### Community 90 - "task-board.blade.php"
Cohesion: 0.29
Nodes (6): cycle({{ $task->id }}), note({{ $task->id }}), open({{ $task->id }}), remove({{ $task->id }}), $set(, toggleStep({{ $step->id }})

### Community 95 - "revision-board.blade.php"
Cohesion: 0.50
Nodes (3): advance({{ $item[, confidence({{ $item[, remove({{ $item[

### Community 97 - "subject-manager.blade.php"
Cohesion: 0.50
Nodes (3): cancel, edit({{ $subject->id }}), remove({{ $subject->id }})

## Knowledge Gaps
- **128 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+123 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **31 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Habit.php`, `Illuminate\Database\Eloquent\Model`, `User.php`, `Subject`, `EventException`, `Event`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Support\Carbon`, `Task`, `PlanningBoard`, `VerifyEmailController.php`, `Illuminate\Database\Eloquent\Builder`, `Deadline`, `Habit`, `Illuminate\Database\Eloquent\Factories\Factory`, `Subtask`, `SendDueReminders`, `Task.php`?**
  _High betweenness centrality (0.179) - this node is a cross-community bridge._
- **Why does `Event` connect `Event` to `Subject`, `User.php`, `Illuminate\Database\Eloquent\Model`, `EventException`, `User`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Support\Carbon`, `PlanningBoard`, `VerifyEmailController.php`, `Illuminate\Database\Eloquent\Builder`, `SnapTime`, `ChangeSeries`, `OccurrenceMoment`, `EventException.php`, `ChangeOccurrence`?**
  _High betweenness centrality (0.062) - this node is a cross-community bridge._
- **Why does `PlanningBoard` connect `PlanningBoard` to `Subject`, `User.php`?**
  _High betweenness centrality (0.024) - this node is a cross-community bridge._
- **Are the 9 inferred relationships involving `User` (e.g. with `.definition()` and `.definition()`) actually correct?**
  _`User` has 9 INFERRED edges - model-reasoned connections that need verification._
- **Are the 2 inferred relationships involving `Event` (e.g. with `.render()` and `.boot()`) actually correct?**
  _`Event` has 2 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _128 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.043478260869565216 - nodes in this community are weakly interconnected._