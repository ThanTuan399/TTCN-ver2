<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\ClubPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClubAdminActionsTest extends TestCase
{
    use RefreshDatabase;

    protected function authHeaderFor(User $user, string $token = 'test-token')
    {
        // set hashed token on user
        $user->api_token = hash('sha256', $token);
        $user->save();

        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_admin_can_approve_post()
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();

        $club = Club::create([
            'name' => 'Test Club',
            'description' => 'desc',
            'created_by' => $admin->id,
        ]);

        ClubMember::create([
            'club_id' => $club->id,
            'user_id' => $admin->id,
            'role' => 'owner',
            'status' => 'approved',
        ]);

        $post = ClubPost::create([
            'club_id' => $club->id,
            'user_id' => $user->id,
            'content' => 'Pending post',
            'status' => 'pending',
        ]);

        $res = $this->postJson("/api/v1/clubs/{$club->id}/posts/{$post->id}/approve", [], $this->authHeaderFor($admin));

        $res->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('club_posts', [
            'id' => $post->id,
            'status' => 'approved',
        ]);
    }

    public function test_admin_can_delete_post()
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();

        $club = Club::create([
            'name' => 'Test Club',
            'description' => 'desc',
            'created_by' => $admin->id,
        ]);

        ClubMember::create([
            'club_id' => $club->id,
            'user_id' => $admin->id,
            'role' => 'admin',
            'status' => 'approved',
        ]);

        $post = ClubPost::create([
            'club_id' => $club->id,
            'user_id' => $user->id,
            'content' => 'Some post',
            'status' => 'approved',
        ]);

        $res = $this->deleteJson("/api/v1/clubs/{$club->id}/posts/{$post->id}", [], $this->authHeaderFor($admin));

        $res->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseMissing('club_posts', ['id' => $post->id]);
    }

    public function test_admin_can_approve_member()
    {
        $admin = User::factory()->create();
        $applicant = User::factory()->create();

        $club = Club::create([
            'name' => 'Test Club',
            'description' => 'desc',
            'created_by' => $admin->id,
        ]);

        ClubMember::create([
            'club_id' => $club->id,
            'user_id' => $admin->id,
            'role' => 'owner',
            'status' => 'approved',
        ]);

        $member = ClubMember::create([
            'club_id' => $club->id,
            'user_id' => $applicant->id,
            'role' => 'member',
            'status' => 'pending',
        ]);

        $res = $this->postJson("/api/v1/clubs/{$club->id}/members/{$member->id}/approve", [], $this->authHeaderFor($admin));

        $res->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('club_members', [
            'id' => $member->id,
            'status' => 'approved',
        ]);
    }

    public function test_admin_can_remove_member()
    {
        $admin = User::factory()->create();
        $memberUser = User::factory()->create();

        $club = Club::create([
            'name' => 'Test Club',
            'description' => 'desc',
            'created_by' => $admin->id,
        ]);

        ClubMember::create([
            'club_id' => $club->id,
            'user_id' => $admin->id,
            'role' => 'owner',
            'status' => 'approved',
        ]);

        $member = ClubMember::create([
            'club_id' => $club->id,
            'user_id' => $memberUser->id,
            'role' => 'member',
            'status' => 'approved',
        ]);

        $res = $this->deleteJson("/api/v1/clubs/{$club->id}/members/{$member->id}", [], $this->authHeaderFor($admin));

        $res->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseMissing('club_members', ['id' => $member->id]);
    }
}
