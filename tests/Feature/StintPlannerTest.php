<?php

namespace Tests\Feature;

use Tests\TestCase;

class StintPlannerTest extends TestCase
{
    public function test_the_stint_planner_page_is_available(): void
    {
        $this->get(route('stint-planner'))
            ->assertOk()
            ->assertSee('BUILD THE')
            ->assertSee('Driver availability')
            ->assertSee('Auto assign available drivers')
            ->assertSee('stint-planner.js');
    }

    public function test_the_excel_template_can_be_downloaded(): void
    {
        $this->get(route('stint-planner.template'))
            ->assertOk()
            ->assertDownload('Toga_Racing_Stint_Planner.xlsx');
    }
}
