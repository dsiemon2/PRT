<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Phase2Seeder extends Seeder
{
    public function run(): void
    {
        // =====================
        // CANNED RESPONSES
        // =====================
        $cannedResponses = [
            [
                'title' => 'Order Status Inquiry',
                'shortcut' => '/orderstatus',
                'category' => 'order',
                'content' => "Hi {{customer.first_name}},\n\nThank you for reaching out! I've checked on your order and here's the current status:\n\nOrder #{{order.number}}\nStatus: [STATUS]\n\nIf you have any other questions, please don't hesitate to ask.\n\nBest regards,\nPecos River Traders Support",
            ],
            [
                'title' => 'Tracking Information',
                'shortcut' => '/tracking',
                'category' => 'shipping',
                'content' => "Hi {{customer.first_name}},\n\nGreat news! Your order has shipped. Here are your tracking details:\n\nOrder #{{order.number}}\nCarrier: [CARRIER]\nTracking Number: [TRACKING]\n\nYou can track your package at: [TRACKING_URL]\n\nPlease allow 24-48 hours for tracking to update.\n\nBest regards,\nPecos River Traders Support",
            ],
            [
                'title' => 'Return Instructions',
                'shortcut' => '/return',
                'category' => 'return',
                'content' => "Hi {{customer.first_name}},\n\nWe're sorry to hear you need to return an item. Here's how to proceed:\n\n1. Pack the item in its original packaging\n2. Include your order number on the return label\n3. Ship to:\n   Pecos River Traders Returns\n   [ADDRESS]\n\nOnce we receive your return, we'll process your refund within 3-5 business days.\n\nBest regards,\nPecos River Traders Support",
            ],
            [
                'title' => 'Apology for Delay',
                'shortcut' => '/delay',
                'category' => 'order',
                'content' => "Hi {{customer.first_name}},\n\nWe sincerely apologize for the delay with your order. We understand how frustrating this can be.\n\n[EXPLANATION]\n\nAs a token of our appreciation for your patience, we'd like to offer you [OFFER].\n\nThank you for your understanding.\n\nBest regards,\nPecos River Traders Support",
            ],
            [
                'title' => 'Product Question',
                'shortcut' => '/product',
                'category' => 'product',
                'content' => "Hi {{customer.first_name}},\n\nThank you for your question about [PRODUCT NAME]!\n\n[ANSWER]\n\nIf you need any additional information, feel free to ask. We're here to help!\n\nBest regards,\nPecos River Traders Support",
            ],
            [
                'title' => 'Size Exchange',
                'shortcut' => '/exchange',
                'category' => 'return',
                'content' => "Hi {{customer.first_name}},\n\nNo problem at all! We'd be happy to help you exchange for a different size.\n\nHere's what we need:\n1. The item you'd like to exchange\n2. The new size you need\n\nWe'll process the exchange and ship your new size as soon as we receive the return.\n\nWould you like us to proceed?\n\nBest regards,\nPecos River Traders Support",
            ],
            [
                'title' => 'Issue Resolved',
                'shortcut' => '/resolved',
                'category' => 'general',
                'content' => "Hi {{customer.first_name}},\n\nGreat news! The issue you reported has been resolved.\n\n[RESOLUTION DETAILS]\n\nIs there anything else we can help you with?\n\nThank you for your patience!\n\nBest regards,\nPecos River Traders Support",
            ],
            [
                'title' => 'Missing Item',
                'shortcut' => '/missing',
                'category' => 'order',
                'content' => "Hi {{customer.first_name}},\n\nWe're so sorry to hear that an item was missing from your order. That's definitely not the experience we want you to have.\n\nWe'll ship the missing item right away at no additional cost to you. You should receive a new tracking number within 24 hours.\n\nAgain, our sincere apologies for the inconvenience.\n\nBest regards,\nPecos River Traders Support",
            ],
        ];

        foreach ($cannedResponses as $response) {
            DB::table('canned_responses')->updateOrInsert(
                ['title' => $response['title']],
                array_merge($response, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Created ' . count($cannedResponses) . ' canned responses');

        // =====================
        // LOYALTY ACHIEVEMENTS
        // =====================
        $achievements = [
            [
                'name' => 'First Purchase',
                'description' => 'Made your first purchase with us!',
                'badge_icon' => 'bi-bag-check',
                'criteria' => json_encode(['type' => 'order_count', 'value' => 1]),
                'points_reward' => 50,
            ],
            [
                'name' => 'Loyal Customer',
                'description' => 'Completed 5 orders',
                'badge_icon' => 'bi-heart',
                'criteria' => json_encode(['type' => 'order_count', 'value' => 5]),
                'points_reward' => 100,
            ],
            [
                'name' => 'Top Spender',
                'description' => 'Spent over $500 lifetime',
                'badge_icon' => 'bi-trophy',
                'criteria' => json_encode(['type' => 'total_spent', 'value' => 500]),
                'points_reward' => 150,
            ],
            [
                'name' => 'VIP Status',
                'description' => 'Spent over $1000 lifetime',
                'badge_icon' => 'bi-star',
                'criteria' => json_encode(['type' => 'total_spent', 'value' => 1000]),
                'points_reward' => 300,
            ],
            [
                'name' => 'Review Writer',
                'description' => 'Left your first product review',
                'badge_icon' => 'bi-chat-quote',
                'criteria' => json_encode(['type' => 'review_count', 'value' => 1]),
                'points_reward' => 25,
            ],
            [
                'name' => 'Review Pro',
                'description' => 'Left 5 product reviews',
                'badge_icon' => 'bi-chat-quote-fill',
                'criteria' => json_encode(['type' => 'review_count', 'value' => 5]),
                'points_reward' => 75,
            ],
            [
                'name' => 'Referral Champion',
                'description' => 'Referred 3 friends who made purchases',
                'badge_icon' => 'bi-people',
                'criteria' => json_encode(['type' => 'referral_count', 'value' => 3]),
                'points_reward' => 200,
            ],
            [
                'name' => 'Anniversary',
                'description' => 'Been a customer for 1 year',
                'badge_icon' => 'bi-calendar-heart',
                'criteria' => json_encode(['type' => 'account_age_days', 'value' => 365]),
                'points_reward' => 100,
            ],
            [
                'name' => 'Boot Enthusiast',
                'description' => 'Purchased 3 pairs of boots',
                'badge_icon' => 'bi-boot',
                'criteria' => json_encode(['type' => 'category_purchases', 'category' => 'boots', 'value' => 3]),
                'points_reward' => 75,
            ],
            [
                'name' => 'Workwear Pro',
                'description' => 'Purchased from Workwear category 5 times',
                'badge_icon' => 'bi-tools',
                'criteria' => json_encode(['type' => 'category_purchases', 'category' => 'workwear', 'value' => 5]),
                'points_reward' => 75,
            ],
        ];

        foreach ($achievements as $achievement) {
            DB::table('loyalty_achievements')->updateOrInsert(
                ['name' => $achievement['name']],
                array_merge($achievement, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Created ' . count($achievements) . ' loyalty achievements');

        // =====================
        // LOYALTY POINT RULES
        // =====================
        $pointRules = [
            [
                'action_type' => 'purchase',
                'name' => 'Standard Purchase Points',
                'description' => 'Earn 1 point for every $1 spent',
                'points_awarded' => 1,
                'points_type' => 'multiplier',
                'conditions' => null,
            ],
            [
                'action_type' => 'first_purchase',
                'name' => 'First Purchase Bonus',
                'description' => 'Bonus 50 points on your first order',
                'points_awarded' => 50,
                'points_type' => 'fixed',
                'conditions' => null,
            ],
            [
                'action_type' => 'review',
                'name' => 'Product Review Points',
                'description' => 'Earn 25 points for leaving a review',
                'points_awarded' => 25,
                'points_type' => 'fixed',
                'conditions' => null,
            ],
            [
                'action_type' => 'referral',
                'name' => 'Referral Points',
                'description' => 'Earn 100 points when a friend makes their first purchase',
                'points_awarded' => 100,
                'points_type' => 'fixed',
                'conditions' => null,
            ],
            [
                'action_type' => 'birthday',
                'name' => 'Birthday Bonus',
                'description' => '2x points on orders during your birthday month',
                'points_awarded' => 2,
                'points_type' => 'multiplier',
                'conditions' => json_encode(['requires_birthday_set' => true]),
            ],
            [
                'action_type' => 'anniversary',
                'name' => 'Anniversary Points',
                'description' => 'Earn 50 points on your account anniversary',
                'points_awarded' => 50,
                'points_type' => 'fixed',
                'conditions' => null,
            ],
            [
                'action_type' => 'social_share',
                'name' => 'Social Share Points',
                'description' => 'Earn 10 points for sharing on social media (once per day)',
                'points_awarded' => 10,
                'points_type' => 'fixed',
                'conditions' => json_encode(['daily_limit' => 1]),
            ],
        ];

        foreach ($pointRules as $rule) {
            DB::table('loyalty_point_rules')->updateOrInsert(
                ['action_type' => $rule['action_type'], 'name' => $rule['name']],
                array_merge($rule, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Created ' . count($pointRules) . ' loyalty point rules');

        // =====================
        // SAMPLE AUTOMATION WORKFLOWS
        // =====================
        $workflows = [
            [
                'name' => 'Abandoned Cart Recovery',
                'description' => 'Send reminder emails when customers abandon their cart',
                'trigger_type' => 'behavior',
                'trigger_config' => json_encode([
                    'event' => 'cart_abandoned',
                    'delay_hours' => 1,
                ]),
                'is_active' => false,
            ],
            [
                'name' => 'Welcome Series',
                'description' => 'Welcome new customers with a 3-email series',
                'trigger_type' => 'event',
                'trigger_config' => json_encode([
                    'event' => 'customer_registered',
                ]),
                'is_active' => false,
            ],
            [
                'name' => 'Post-Purchase Follow-up',
                'description' => 'Thank customers and request reviews after delivery',
                'trigger_type' => 'event',
                'trigger_config' => json_encode([
                    'event' => 'order_delivered',
                    'delay_days' => 7,
                ]),
                'is_active' => false,
            ],
            [
                'name' => 'Win-Back Campaign',
                'description' => 'Re-engage customers who havent ordered in 60+ days',
                'trigger_type' => 'threshold',
                'trigger_config' => json_encode([
                    'metric' => 'days_since_last_order',
                    'threshold' => 60,
                ]),
                'is_active' => false,
            ],
            [
                'name' => 'Birthday Email',
                'description' => 'Send birthday wishes with special offer',
                'trigger_type' => 'event',
                'trigger_config' => json_encode([
                    'event' => 'customer_birthday',
                    'days_before' => 3,
                ]),
                'is_active' => false,
            ],
        ];

        foreach ($workflows as $workflow) {
            DB::table('automation_workflows')->updateOrInsert(
                ['name' => $workflow['name']],
                array_merge($workflow, [
                    'stats' => json_encode(['sent' => 0, 'opened' => 0, 'clicked' => 0, 'converted' => 0]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Created ' . count($workflows) . ' automation workflows');

        // =====================
        // SAMPLE SUPPORT TICKETS
        // =====================
        $this->createSampleTickets();
    }

    private function createSampleTickets()
    {
        // Get some customer IDs
        $customers = DB::table('users')
            ->whereNotNull('Email')
            ->limit(10)
            ->pluck('id')
            ->toArray();

        if (empty($customers)) {
            $this->command->warn('No customers found, skipping sample tickets');
            return;
        }

        $categories = ['order', 'return', 'product', 'shipping', 'billing', 'other'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $statuses = ['open', 'in_progress', 'pending_customer', 'resolved', 'closed'];

        $sampleTickets = [
            ['subject' => 'Order not received', 'category' => 'shipping', 'priority' => 'high'],
            ['subject' => 'Wrong size sent', 'category' => 'order', 'priority' => 'medium'],
            ['subject' => 'Request for exchange', 'category' => 'return', 'priority' => 'medium'],
            ['subject' => 'Product defect', 'category' => 'product', 'priority' => 'high'],
            ['subject' => 'Billing discrepancy', 'category' => 'billing', 'priority' => 'medium'],
            ['subject' => 'Tracking not updating', 'category' => 'shipping', 'priority' => 'low'],
            ['subject' => 'Boot sizing question', 'category' => 'product', 'priority' => 'low'],
            ['subject' => 'Cancel order request', 'category' => 'order', 'priority' => 'urgent'],
        ];

        $ticketCount = 0;
        foreach ($sampleTickets as $index => $ticketData) {
            $customerId = $customers[array_rand($customers)];
            $ticketNumber = 'TKT-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT);

            // Check if ticket already exists
            $exists = DB::table('support_tickets')->where('ticket_number', $ticketNumber)->exists();
            if ($exists) {
                continue;
            }

            $status = $statuses[array_rand($statuses)];
            $createdAt = now()->subDays(rand(1, 30))->subHours(rand(1, 23));

            $ticketId = DB::table('support_tickets')->insertGetId([
                'ticket_number' => $ticketNumber,
                'customer_id' => $customerId,
                'subject' => $ticketData['subject'],
                'category' => $ticketData['category'],
                'priority' => $ticketData['priority'],
                'status' => $status,
                'first_response_at' => in_array($status, ['in_progress', 'pending_customer', 'resolved', 'closed'])
                    ? $createdAt->copy()->addHours(rand(1, 8))
                    : null,
                'resolved_at' => in_array($status, ['resolved', 'closed'])
                    ? $createdAt->copy()->addDays(rand(1, 3))
                    : null,
                'satisfaction_rating' => $status === 'closed' ? rand(3, 5) : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Add initial message
            DB::table('ticket_messages')->insert([
                'ticket_id' => $ticketId,
                'sender_type' => 'customer',
                'sender_id' => $customerId,
                'message' => "Hello, I need help with: {$ticketData['subject']}. Please assist.",
                'is_internal' => false,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Add staff response if not open
            if ($status !== 'open') {
                DB::table('ticket_messages')->insert([
                    'ticket_id' => $ticketId,
                    'sender_type' => 'staff',
                    'sender_id' => 1,
                    'message' => "Hi! Thank you for contacting us. We're looking into this and will get back to you shortly.",
                    'is_internal' => false,
                    'created_at' => $createdAt->copy()->addHours(rand(1, 4)),
                    'updated_at' => $createdAt->copy()->addHours(rand(1, 4)),
                ]);
            }

            $ticketCount++;
        }

        $this->command->info("Created {$ticketCount} sample support tickets");
    }
}
