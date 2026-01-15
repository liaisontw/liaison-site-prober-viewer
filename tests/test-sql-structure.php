<?php

class Test_Liaisipv_SQL_Structure extends WP_UnitTestCase {

    protected $table;

    public function setUp(): void {
        parent::setUp();

        global $wpdb;
        $this->table = $wpdb->prefix . 'wpsp_activity';

        // 建立測試資料表（最小結構）
        $wpdb->query( "
            CREATE TABLE IF NOT EXISTS {$this->table} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                created_at DATETIME NOT NULL,
                user_id BIGINT UNSIGNED NOT NULL,
                ip VARCHAR(45),
                action VARCHAR(100),
                object_type VARCHAR(100),
                description TEXT,
                PRIMARY KEY (id)
            )
        " );

        // 插入一筆測試資料
        $wpdb->insert(
            $this->table,
            [
                'created_at'  => current_time( 'mysql' ),
                'user_id'     => 1,
                'ip'          => '127.0.0.1',
                'action'      => 'test_action',
                'object_type' => 'post',
                'description' => 'test description',
            ]
        );

        // 模擬 plugin 使用的 table name
        $wpdb->wpsp_activity = $this->table;
    }

    /** @test */
    public function sql_result_has_expected_structure() {

        global $wpdb;

        $rows = $wpdb->get_results(
            "SELECT
                id,
                created_at,
                user_id,
                ip,
                action,
                object_type,
                description
             FROM {$wpdb->wpsp_activity}
             LIMIT 1",
            ARRAY_A
        );

        $this->assertIsArray( $rows );
        $this->assertNotEmpty( $rows );

        $row = $rows[0];

        $this->assertArrayHasKey( 'id', $row );
        $this->assertArrayHasKey( 'created_at', $row );
        $this->assertArrayHasKey( 'user_id', $row );
        $this->assertArrayHasKey( 'ip', $row );
        $this->assertArrayHasKey( 'action', $row );
        $this->assertArrayHasKey( 'object_type', $row );
        $this->assertArrayHasKey( 'description', $row );
    }
}
