<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ChildOfficerFollowUpAccessTest extends TestCase
{
    public function test_only_child_officers_can_open_follow_ups(): void
    {
        foreach (['admin', 'field_officer', 'enumerator'] as $role) {
            $user = new User([
                'name' => ucfirst(str_replace('_', ' ', $role)),
                'email' => $role . '@example.test',
                'password' => 'password',
                'role' => $role,
            ]);

            $this->actingAs($user)
                ->get(route('child_officer.follow-ups.index'))
                ->assertForbidden();
        }
    }
}