<?php

use Silber\Bouncer\Database\Role;
use Silber\Bouncer\Database\Ability;

class BouncerSimpleTest extends BaseTestCase
{
    public function test_bouncer_can_give_and_remove_abilities()
    {
        $bouncer = $this->bouncer($user = User::create());

        $bouncer->allow($user)->to('edit-site');

        $this->assertTrue($bouncer->allows('edit-site'));

        $bouncer->disallow($user)->to('edit-site');
        $this->clipboard->refresh();

        $this->assertTrue($bouncer->denies('edit-site'));
    }

    public function test_bouncer_can_give_and_remove_roles()
    {
        $bouncer = $this->bouncer($user = User::create());

        $bouncer->allow('admin')->to('edit-site');
        $bouncer->assign('admin')->to($user);

        $this->assertTrue($bouncer->allows('edit-site'));

        $bouncer->retract('admin')->from($user);
        $this->clipboard->refresh();

        $this->assertTrue($bouncer->denies('edit-site'));
    }

    public function test_bouncer_can_disallow_abilities_on_roles()
    {
        $bouncer = $this->bouncer($user = User::create());

        $bouncer->allow('admin')->to('edit-site');
        $bouncer->disallow('admin')->to('edit-site');
        $bouncer->assign('admin')->to($user);

        $this->assertTrue($bouncer->denies('edit-site'));
    }

    public function test_bouncer_can_check_user_roles()
    {
        $bouncer = $this->bouncer($user = User::create());

        $bouncer->assign('moderator')->to($user);
        $bouncer->assign('editor')->to($user);

        $this->assertTrue($bouncer->is($user)->a('moderator'));
        $this->assertTrue($bouncer->is($user)->an('editor'));
        $this->assertFalse($bouncer->is($user)->an('admin'));
    }

    public function test_bouncer_can_check_multiple_user_roles()
    {
        $bouncer = $this->bouncer($user = User::create());

        $bouncer->assign('moderator')->to($user);
        $bouncer->assign('editor')->to($user);

        $this->assertTrue($bouncer->is($user)->a('moderator', 'admin'));
        $this->assertTrue($bouncer->is($user)->an('editor', 'moderator'));
        $this->assertTrue($bouncer->is($user)->all('editor', 'moderator'));
        $this->assertFalse($bouncer->is($user)->all('admin', 'moderator'));
    }

    public function test_bouncer_can_get_an_empty_role_model()
    {
        $bouncer = $this->bouncer($user = User::create());

        $this->assertInstanceOf(Role::class, $bouncer->role());
    }

    public function test_bouncer_can_fill_a_role_model()
    {
        $bouncer = $this->bouncer($user = User::create());
        $role = $bouncer->role(['name' => 'test-role']);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('test-role', $role->name);
    }

    public function test_bouncer_can_get_an_empty_ability_model()
    {
        $bouncer = $this->bouncer($user = User::create());

        $this->assertInstanceOf(Ability::class, $bouncer->ability());
    }

    public function test_bouncer_can_fill_an_ability_model()
    {
        $bouncer = $this->bouncer($user = User::create());
        $ability = $bouncer->ability(['name' => 'test-ability']);

        $this->assertInstanceOf(Ability::class, $ability);
        $this->assertEquals('test-ability', $ability->name);
    }
}
