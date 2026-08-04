<?php

namespace Tests\Feature;

use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\ContactMessage;
use App\Models\User;
use Database\Seeders\PortfolioCmsPermissionSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CmsPermissionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_permission_seeder_creates_roles_and_super_admin(): void
    {
        $this->seed(PortfolioCmsPermissionSeeder::class);

        $admin = User::where('email', 'fachru2006@gmail.com')->firstOrFail();

        $this->assertTrue($admin->hasRole('Super Admin'));
        $this->assertTrue($admin->canAccessPanel(filament()->getPanel('admin')));
    }

    public function test_content_editor_can_manage_cms_but_not_roles_permissions(): void
    {
        $this->seed(PortfolioCmsPermissionSeeder::class);

        $editor = User::factory()->create();
        $editor->assignRole('Content Editor');

        $this->actingAs($editor);

        $this->assertTrue(ProjectResource::canCreate());
        $this->assertFalse(RoleResource::canViewAny());
    }

    public function test_message_viewer_can_update_contact_status_but_cannot_create_or_edit_messages(): void
    {
        $this->seed(PortfolioCmsPermissionSeeder::class);

        $viewer = User::factory()->create();
        $viewer->assignRole('Message Viewer');

        $message = ContactMessage::create([
            'name' => 'Visitor',
            'email' => 'visitor@example.com',
            'subject' => 'Need help',
            'message' => 'Please reply.',
        ]);

        $this->actingAs($viewer);

        $this->assertTrue($viewer->can('update', $message));
        $this->assertFalse(ContactMessageResource::canCreate());
        $this->assertFalse(ContactMessageResource::canEdit($message));
    }
}
