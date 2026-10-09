<?php

/**
 * Small, dependency-free chart + tile builders shared by the Staff and Vendor
 * dashboards. Each function returns an HTML string; every piece of text that
 * comes from data is escaped here, so views can echo the result as-is.
 *
 * Styling lives in partials/dashboard_viz_assets.php (colour tokens, light and
 * dark, plus the hover tooltip script). Charts carry a plain-text summary
 * (aria-label) and a visually hidden table so nothing depends on colour or
 * on seeing the picture.
 */

if (! function_exists('dash_fmt')) {
    function dash_fmt($n): string
    {
        return number_format((float) $n);
    }
}

if (! function_exists('dash_sr_table')) {
    /** @param list<array{0:string,1:string}> $rows [label, value] */
    function dash_sr_table(string $caption, array $rows): string
    {
        $h = '<table class="sr-only"><caption>' . esc($caption) . '</caption><tbody>';
        foreach ($rows as [$label, $value]) {
            $h .= '<tr><th scope="row">' . esc($label) . '</th><td>' . esc($value) . '</td></tr>';
        }

        return $h . '</tbody></table>';
    }
}

if (! function_exists('dash_hbars')) {
    /**
     * Horizontal bars, one series. Items: label, value, optional href, note,
     * tone (1-4 → light-to-dark ramp, for ordered stages; default = series blue).
     *
     * @param list<array<string,mixed>> $items
     */
    function dash_hbars(array $items, string $unit = '', string $ariaLabel = ''): string
    {
        if ($items === []) {
            return '<p class="text-sm text-slate-500 dark:text-slate-400">No data yet.</p>';
        }

        $max = max(1.0, max(array_map(static fn($i) => (float) $i['value'], $items)));

        $h    = '<div class="viz-hbars" role="img" aria-label="' . esc($ariaLabel, 'attr') . '">';
        $rows = [];
        foreach ($items as $i) {
            $v     = (float) $i['value'];
            $pct   = $v <= 0 ? 0 : max(2, round($v / $max * 100, 1));
            $tip   = $i['label'] . ': ' . dash_fmt($v) . ($unit !== '' ? ' ' . $unit : '') . (! empty($i['note']) ? ' · ' . $i['note'] : '');
            $tone  = isset($i['tone']) ? ' viz-tone-' . (int) $i['tone'] : '';
            $label = esc((string) $i['label']);
            $labelHtml = ! empty($i['href'])
                ? '<a class="viz-hbar-label" href="' . esc((string) $i['href'], 'attr') . '">' . $label . '</a>'
                : '<span class="viz-hbar-label">' . $label . '</span>';

            $h .= '<div class="viz-hbar-row" tabindex="0" data-tip="' . esc($tip, 'attr') . '">'
                . $labelHtml
                . '<div class="viz-hbar-track"><div class="viz-hbar-fill' . $tone . '" style="width:' . $pct . '%"></div></div>'
                . '<span class="viz-hbar-value">' . dash_fmt($v) . '</span>'
                . '</div>';

            $rows[] = [(string) $i['label'], dash_fmt($v) . ($unit !== '' ? ' ' . $unit : '')];
        }

        return $h . '</div>' . dash_sr_table($ariaLabel, $rows);
    }
}

if (! function_exists('dash_columns')) {
    /**
     * Vertical columns over time, one series. Only the peak and the latest
     * column carry a printed value; every column has a hover tooltip.
     *
     * @param list<string> $labels
     * @param list<int|float> $values
     */
    function dash_columns(array $labels, array $values, string $unit = '', string $ariaLabel = ''): string
    {
        if ($labels === [] || array_sum($values) <= 0) {
            return '<p class="text-sm text-slate-500 dark:text-slate-400">No activity in this period.</p>';
        }

        $max     = max(1.0, (float) max($values));
        $peakIdx = (int) array_search(max($values), $values, true);
        $lastIdx = count($values) - 1;

        $h    = '<div class="viz-cols" role="img" aria-label="' . esc($ariaLabel, 'attr') . '">';
        $rows = [];
        foreach ($labels as $idx => $label) {
            $v    = (float) ($values[$idx] ?? 0);
            $pct  = $v <= 0 ? 0 : max(3, round($v / $max * 100, 1));
            $tip  = $label . ': ' . dash_fmt($v) . ($unit !== '' ? ' ' . $unit : '');
            $show = ($idx === $peakIdx || $idx === $lastIdx) && $v > 0;

            $h .= '<div class="viz-col" tabindex="0" data-tip="' . esc($tip, 'attr') . '">'
                . '<div class="viz-col-area">'
                . ($show ? '<span class="viz-col-val" style="bottom:calc(' . $pct . '% + 4px)">' . dash_fmt($v) . '</span>' : '')
                . '<div class="viz-col-bar" style="height:' . $pct . '%"></div>'
                . '</div>'
                . '<span class="viz-col-label">' . esc((string) $label) . '</span>'
                . '</div>';

            $rows[] = [(string) $label, dash_fmt($v) . ($unit !== '' ? ' ' . $unit : '')];
        }

        return $h . '</div>' . dash_sr_table($ariaLabel, $rows);
    }
}

if (! function_exists('dash_stacked')) {
    /**
     * One horizontal stacked bar (part-to-whole) with a legend that always
     * shows label, count and share — identity never relies on colour alone.
     * Segments take categorical slots 1-3 in order.
     *
     * @param list<array{label:string,value:int|float}> $segments
     */
    function dash_stacked(array $segments, string $ariaLabel = ''): string
    {
        $segments = array_values(array_filter($segments, static fn($s) => (float) $s['value'] > 0));
        $total    = array_sum(array_map(static fn($s) => (float) $s['value'], $segments));

        if ($total <= 0) {
            return '<p class="text-sm text-slate-500 dark:text-slate-400">No data yet.</p>';
        }

        $bar    = '<div class="viz-stack" role="img" aria-label="' . esc($ariaLabel, 'attr') . '">';
        $legend = '<ul class="viz-legend">';
        $rows   = [];
        foreach ($segments as $idx => $s) {
            $slot  = min(3, $idx + 1);
            $share = $s['value'] / $total * 100;
            $tip   = $s['label'] . ': ' . dash_fmt($s['value']) . ' (' . round($share) . '%)';

            $bar    .= '<div class="viz-stack-seg viz-s' . $slot . '" style="flex:' . (float) $s['value'] . ' 1 0" tabindex="0" data-tip="' . esc($tip, 'attr') . '"></div>';
            $legend .= '<li><span class="viz-swatch viz-s' . $slot . '"></span><span class="viz-legend-label">' . esc((string) $s['label']) . '</span> '
                . '<strong>' . dash_fmt($s['value']) . '</strong> <span class="viz-legend-pct">' . round($share) . '%</span></li>';
            $rows[] = [(string) $s['label'], dash_fmt($s['value']) . ' (' . round($share) . '%)'];
        }

        return $bar . '</div>' . $legend . '</ul>' . dash_sr_table($ariaLabel, $rows);
    }
}

if (! function_exists('dash_stat')) {
    /**
     * Headline tile. $href makes the whole tile a link. $tone only tints the
     * icon (never the number) — warn / bad / good / neutral.
     */
    function dash_stat(string $label, $value, string $sub = '', string $icon = 'info', string $tone = 'neutral', ?string $href = null): string
    {
        $tones = [
            'neutral' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
            'info'    => 'bg-primary/10 text-primary',
            'good'    => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
            'warn'    => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
            'bad'     => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        ];
        $iconClass = $tones[$tone] ?? $tones['neutral'];

        $inner = '<div class="flex items-start justify-between gap-3">'
            . '<div class="min-w-0">'
            . '<p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">' . esc($label) . '</p>'
            . '<p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">' . (is_numeric($value) ? dash_fmt($value) : esc((string) $value)) . '</p>'
            . ($sub !== '' ? '<p class="mt-1 text-xs text-slate-500 dark:text-slate-400">' . esc($sub) . '</p>' : '')
            . '</div>'
            . '<span class="flex size-10 shrink-0 items-center justify-center rounded-full ' . $iconClass . '"><span class="material-symbols-outlined text-[22px]">' . esc($icon) . '</span></span>'
            . '</div>';

        $class = 'block rounded-xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark';

        return $href !== null
            ? '<a href="' . esc($href, 'attr') . '" class="' . $class . ' transition-colors hover:border-primary">' . $inner . '</a>'
            : '<div class="' . $class . '">' . $inner . '</div>';
    }
}

if (! function_exists('dash_status_pill')) {
    /** Status chip: icon + label carry the meaning; colour only tints the icon. */
    function dash_status_pill(string $status): string
    {
        $map = [
            'pending'    => ['schedule', 'text-amber-600', 'Pending'],
            'draft'      => ['edit_note', 'text-slate-400', 'Draft'],
            'approved'   => ['check_circle', 'text-emerald-600', 'Approved'],
            'rejected'   => ['cancel', 'text-red-600', 'Rejected'],
            'active'     => ['check_circle', 'text-emerald-600', 'Active'],
            'inactive'   => ['pause_circle', 'text-slate-400', 'Inactive'],
            'suspended'  => ['block', 'text-amber-600', 'Suspended'],
            'terminated' => ['cancel', 'text-red-600', 'Terminated'],
            'expired'    => ['event_busy', 'text-red-600', 'Expired'],
        ];
        $key = strtolower(trim($status));
        [$icon, $color, $label] = $map[$key] ?? ['help', 'text-slate-400', $status !== '' ? $status : '—'];

        return '<span class="inline-flex items-center gap-1 text-xs font-medium text-slate-700 dark:text-slate-200">'
            . '<span class="material-symbols-outlined text-[16px] ' . $color . '">' . $icon . '</span>' . esc($label) . '</span>';
    }
}
