<?php

declare(strict_types=1);

namespace App\Actions\Event;

final class ReadPhrase
{
    private const RANGE = '/(?<!\d)(?<sh>\d{1,2})(?:\s*h\s*(?<sm>\d{2})?|\s*:\s*(?<sm2>\d{2}))\s*(?:-|–|—|à|a)\s*(?<eh>\d{1,2})(?:\s*h\s*(?<em>\d{2})?|\s*:\s*(?<em2>\d{2}))?(?!\d)/u';

    private const BARE = '/(?<!\d)(?<sh>\d{1,2})\s*(?:-|–|—)\s*(?<eh>\d{1,2})(?!\d)/u';

    private const WEEKDAY = '/\b(lundi|mardi|mercredi|jeudi|vendredi|samedi|dimanche|lun|mar|mer|jeu|ven|sam|dim)\b/u';

    private const WEEKLY = '/\b(chaque semaine|toutes les semaines|hebdomadaire)\b/u';

    private const ROOM = '/\b(?:salle|amphi)\s+([a-z0-9]+)/u';

    /** @var array<string, int> */
    private const DAYS = [
        'lundi' => 1,
        'lun' => 1,
        'mardi' => 2,
        'mar' => 2,
        'mercredi' => 3,
        'mer' => 3,
        'jeudi' => 4,
        'jeu' => 4,
        'vendredi' => 5,
        'ven' => 5,
        'samedi' => 6,
        'sam' => 6,
        'dimanche' => 0,
        'dim' => 0,
    ];

    /** @var array<string, string> */
    private const KINDS = [
        'tp' => 'TD/TP',
        'td' => 'TD/TP',
        'cours' => 'Cours',
        'cm' => 'Cours',
        'révision' => 'Révisions',
        'revision' => 'Révisions',
        'révisions' => 'Révisions',
        'revisions' => 'Révisions',
        'projet' => 'Projet',
        'sport' => 'Sport',
        'ctf' => 'Cybersécurité/CTF',
        'cyber' => 'Cybersécurité/CTF',
        'cybersécurité' => 'Cybersécurité/CTF',
        'cybersecurite' => 'Cybersécurité/CTF',
        'exposé' => 'Exposé',
        'expose' => 'Exposé',
        'repas' => 'Repas',
        'transport' => 'Transport',
        'repos' => 'Repos',
        'sommeil' => 'Sommeil',
        'perso' => 'Perso',
    ];

    /** @var list<string> */
    private const ACRONYMS = ['tp', 'td', 'cm', 'ctf'];

    public function __invoke(string $phrase): ?Phrase
    {
        $text = mb_strtolower(trim($phrase));
        $text = trim((string) preg_replace('/\s+/u', ' ', $text));

        if ($text === '') {
            return null;
        }

        $location = '';

        if (preg_match(self::ROOM, $text, $room) === 1) {
            $location = mb_strtoupper($room[1]);
            $text = $this->without($text, $room[0]);
        }

        $weekly = false;

        if (preg_match(self::WEEKLY, $text, $weeklyMatch) === 1) {
            $weekly = true;
            $text = $this->without($text, $weeklyMatch[0]);
        }

        $starts = null;
        $ends = null;
        $range = $this->range($text, self::RANGE) ?? $this->range($text, self::BARE);

        if ($range !== null) {
            [$text, $starts, $ends] = $range;
        }

        $weekday = null;

        if (preg_match(self::WEEKDAY, $text, $day) === 1) {
            $weekday = self::DAYS[$day[1]];
            $text = $this->without($text, $day[0]);
        }

        $text = trim((string) preg_replace('/\b(?:le|la|les|un|une|au|du|de|à|a)\b/u', ' ', $text));
        $text = trim((string) preg_replace('/\s+/u', ' ', $text));

        $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $words = is_array($words) ? $words : [];
        $first = $words[0] ?? '';
        $category = self::KINDS[$first] ?? null;
        $rest = $category === null ? $words : array_slice($words, 1);

        return new Phrase(
            $this->label($words),
            $category !== null && $rest === [] ? '' : $this->label($rest),
            $weekday,
            $starts,
            $ends,
            $category,
            $location,
            $weekly,
        );
    }

    /**
     * @return array{0: string, 1: string, 2: string}|null
     */
    private function range(string $text, string $pattern): ?array
    {
        if (preg_match($pattern, $text, $matches) !== 1) {
            return null;
        }

        $starts = $this->clock($matches['sh'] ?? '', $this->minute($matches, 'sm', 'sm2'));
        $ends = $this->clock($matches['eh'] ?? '', $this->minute($matches, 'em', 'em2'));

        if ($starts === null || $ends === null) {
            return null;
        }

        return [$this->without($text, $matches[0]), $starts, $ends];
    }

    /**
     * @param  array<int|string, string>  $matches
     */
    private function minute(array $matches, string $key, string $alternate): string
    {
        $minute = $matches[$key] ?? '';

        if ($minute !== '') {
            return $minute;
        }

        return $matches[$alternate] ?? '';
    }

    private function clock(string $hour, string $minute): ?string
    {
        if ($hour === '' || preg_match('/^\d{1,2}$/', $hour) !== 1) {
            return null;
        }

        if ($minute !== '' && preg_match('/^\d{2}$/', $minute) !== 1) {
            return null;
        }

        $hours = (int) $hour;
        $minutes = $minute === '' ? 0 : (int) $minute;

        if ($hours > 23 || $minutes > 59) {
            return null;
        }

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    private function without(string $text, string $piece): string
    {
        $cleared = trim(str_replace($piece, ' ', $text));

        return trim((string) preg_replace('/\s+/u', ' ', $cleared));
    }

    /**
     * @param  list<string>  $words
     */
    private function label(array $words): string
    {
        $labels = [];

        foreach ($words as $word) {
            if (in_array($word, self::ACRONYMS, true)) {
                $labels[] = mb_strtoupper($word);

                continue;
            }

            $labels[] = mb_strtoupper(mb_substr($word, 0, 1)).mb_substr($word, 1);
        }

        return implode(' ', $labels);
    }
}
