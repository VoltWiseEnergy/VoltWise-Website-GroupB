<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            // ── Streak Badges ──────────────────────────────────────────────
            [
                'key'         => 'first_log',
                'name'        => 'First Step',
                'description' => 'Log your device usage for the very first time.',
                'emoji'       => '🌱',
                'category'    => 'streak',
                'color'       => '#10b981',
            ],
            [
                'key'         => 'week_streak',
                'name'        => 'Week Warrior',
                'description' => 'Log your usage for 7 consecutive days.',
                'emoji'       => '🔥',
                'category'    => 'streak',
                'color'       => '#f97316',
            ],
            [
                'key'         => 'month_streak',
                'name'        => 'Monthly Champion',
                'description' => 'Log your usage for 30 consecutive days.',
                'emoji'       => '🏆',
                'category'    => 'streak',
                'color'       => '#f59e0b',
            ],

            // ── Savings Badges ─────────────────────────────────────────────
            [
                'key'         => 'budget_starter',
                'name'        => 'Budget Conscious',
                'description' => 'Stay under your monthly budget 3 times.',
                'emoji'       => '💰',
                'category'    => 'savings',
                'color'       => '#3b82f6',
            ],
            [
                'key'         => 'budget_keeper',
                'name'        => 'Penny Pincher',
                'description' => 'Stay under your monthly budget 7 times.',
                'emoji'       => '🏦',
                'category'    => 'savings',
                'color'       => '#6366f1',
            ],
            [
                'key'         => 'budget_master',
                'name'        => 'Budget Master',
                'description' => 'Stay under your monthly budget 30 times.',
                'emoji'       => '💎',
                'category'    => 'savings',
                'color'       => '#8b5cf6',
            ],

            // ── Usage Badges ───────────────────────────────────────────────
            [
                'key'         => 'saver_5',
                'name'        => 'Energy Saver',
                'description' => 'Earn a Low-Usage Day reward 5 times.',
                'emoji'       => '⚡',
                'category'    => 'usage',
                'color'       => '#4A7CF6',
            ],
            [
                'key'         => 'eco_5',
                'name'        => 'Eco Warrior',
                'description' => 'Earn a Very Low Usage reward 5 times.',
                'emoji'       => '🌿',
                'category'    => 'usage',
                'color'       => '#059669',
            ],
            [
                'key'         => 'eco_15',
                'name'        => 'Green Champion',
                'description' => 'Earn a Very Low Usage reward 15 times.',
                'emoji'       => '🌍',
                'category'    => 'usage',
                'color'       => '#065f46',
            ],

            // ── Milestone Badges ───────────────────────────────────────────
            [
                'key'         => 'pts_100',
                'name'        => 'Powered Up',
                'description' => 'Reach 100 total points and hit Silver level.',
                'emoji'       => '✨',
                'category'    => 'milestone',
                'color'       => '#94a3b8',
            ],
            [
                'key'         => 'pts_300',
                'name'        => 'High Voltage',
                'description' => 'Reach 300 total points and hit Gold level.',
                'emoji'       => '⚡',
                'category'    => 'milestone',
                'color'       => '#f59e0b',
            ],
            [
                'key'         => 'pts_700',
                'name'        => 'VoltWise Legend',
                'description' => 'Reach 700 total points and achieve Platinum.',
                'emoji'       => '👑',
                'category'    => 'milestone',
                'color'       => '#8b5cf6',
            ],
        ];

        foreach ($badges as $data) {
            Badge::updateOrCreate(['key' => $data['key']], $data);
        }
    }
}
