<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PostgresPoolingTest extends TestCase
{
    public function test_statement_cleanup_keeps_a_pooled_transaction_usable(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            $this->markTestSkipped('Requires PostgreSQL; run through PgBouncer to cover transaction pooling.');
        }

        DB::beginTransaction();
        try {
            $statement = DB::getPdo()->prepare('SELECT CAST(:amount AS integer)');
            $statement->execute(['amount' => 7]);
            $this->assertSame(7, $statement->fetchColumn());
            // PHP 8.2 sends SQL DEALLOCATE for named statements here. A pooler
            // can reject that silently, leaving the next query with 25P02.
            unset($statement);
            $this->assertSame(1, DB::selectOne('SELECT 1 AS healthy')->healthy);
        } finally {
            DB::rollBack();
        }
    }
}
