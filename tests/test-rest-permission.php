<?php

class Test_Liaisipv_REST_Permission extends WP_UnitTestCase {

    public function setUp(): void {
        parent::setUp();
        // 確保 REST routes 已註冊
        do_action( 'rest_api_init' );
    }

    /** @test */
    public function subscriber_cannot_access_logs_endpoint() {

        $user_id = self::factory()->user->create([
            'role' => 'subscriber',
        ]);
        wp_set_current_user( $user_id );

        $request  = new WP_REST_Request( 'GET', '/site-prober/v1/logs' );
        $response = rest_do_request( $request );

        $this->assertEquals( 404, $response->get_status() );
    }

    /** @test */
    public function admin_can_access_logs_endpoint() {

        $user_id = self::factory()->user->create([
            'role' => 'administrator',
        ]);
        wp_set_current_user( $user_id );

        $request  = new WP_REST_Request( 'GET', '/site-prober/v1/logs' );
        $response = rest_do_request( $request );

        $this->assertNotEquals( 403, $response->get_status() );
    }
}
