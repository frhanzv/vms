<?php
/**
 * Sidebar for report pages (and Config / kiosk settings pages that include it).
 *
 * This used to be a second, hand-maintained copy of the main sidebar, which is
 * why it kept missing new menu items (Staff Pass pipeline, Dashboard group,
 * List Columns, vendor-account rules...). It now simply renders
 * partials/sidebar so there is ONE sidebar to maintain.
 *
 * @var string|null $current  route path of the page (optional)
 */
echo view('partials/sidebar', ['current' => $current ?? null]);
