<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $isMysql = DB::getDriverName() === 'mysql';

        // ── USERS ────────────────────────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->string('email_hash', 64)->nullable()->after('email');
        });

        if ($isMysql) {
            DB::statement('ALTER TABLE users MODIFY COLUMN name TEXT NOT NULL');
            DB::statement('ALTER TABLE users MODIFY COLUMN email TEXT NOT NULL');
        }

        DB::table('users')->get()->each(function ($row) {
            DB::table('users')->where('id', $row->id)->update([
                'name'       => Crypt::encrypt($row->name),
                'email'      => Crypt::encrypt($row->email),
                'email_hash' => hash('sha256', strtolower(trim($row->email))),
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email_hash', 64)->nullable(false)->unique()->change();
        });

        // ── PARTICIPANTS ─────────────────────────────────────────────────────
        Schema::table('participants', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->string('email_hash', 64)->nullable()->after('email');
            $table->text('search_index')->nullable()->after('email_hash');
        });

        if ($isMysql) {
            DB::statement('ALTER TABLE participants MODIFY COLUMN first_name TEXT NOT NULL');
            DB::statement('ALTER TABLE participants MODIFY COLUMN last_name TEXT NOT NULL');
            DB::statement('ALTER TABLE participants MODIFY COLUMN email TEXT NOT NULL');
            DB::statement('ALTER TABLE participants MODIFY COLUMN phone TEXT NULL');
            DB::statement('ALTER TABLE participants MODIFY COLUMN university TEXT NULL');
            DB::statement('ALTER TABLE participants MODIFY COLUMN major TEXT NULL');
        }

        DB::table('participants')->get()->each(function ($row) {
            DB::table('participants')->where('id', $row->id)->update([
                'first_name'   => Crypt::encrypt($row->first_name),
                'last_name'    => Crypt::encrypt($row->last_name),
                'email'        => Crypt::encrypt($row->email),
                'phone'        => $row->phone !== null ? Crypt::encrypt($row->phone) : null,
                'university'   => $row->university !== null ? Crypt::encrypt($row->university) : null,
                'major'        => $row->major !== null ? Crypt::encrypt($row->major) : null,
                'email_hash'   => hash('sha256', strtolower(trim($row->email))),
                'search_index' => strtolower(trim($row->first_name . ' ' . $row->last_name . ' ' . $row->email)),
            ]);
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->string('email_hash', 64)->nullable(false)->unique()->change();
        });

        // ── JUDGES ───────────────────────────────────────────────────────────
        Schema::table('judges', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->string('email_hash', 64)->nullable()->after('email');
            $table->text('search_index')->nullable()->after('email_hash');
        });

        if ($isMysql) {
            DB::statement('ALTER TABLE judges MODIFY COLUMN first_name TEXT NOT NULL');
            DB::statement('ALTER TABLE judges MODIFY COLUMN last_name TEXT NOT NULL');
            DB::statement('ALTER TABLE judges MODIFY COLUMN email TEXT NOT NULL');
            DB::statement('ALTER TABLE judges MODIFY COLUMN company TEXT NULL');
        }

        DB::table('judges')->get()->each(function ($row) {
            DB::table('judges')->where('id', $row->id)->update([
                'first_name'   => Crypt::encrypt($row->first_name),
                'last_name'    => Crypt::encrypt($row->last_name),
                'email'        => Crypt::encrypt($row->email),
                'company'      => $row->company !== null ? Crypt::encrypt($row->company) : null,
                'email_hash'   => hash('sha256', strtolower(trim($row->email))),
                'search_index' => strtolower(trim($row->first_name . ' ' . $row->last_name . ' ' . $row->email)),
            ]);
        });

        Schema::table('judges', function (Blueprint $table) {
            $table->string('email_hash', 64)->nullable(false)->unique()->change();
        });

        // ── MENTORS ──────────────────────────────────────────────────────────
        Schema::table('mentors', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->string('email_hash', 64)->nullable()->after('email');
            $table->text('search_index')->nullable()->after('email_hash');
        });

        if ($isMysql) {
            DB::statement('ALTER TABLE mentors MODIFY COLUMN first_name TEXT NOT NULL');
            DB::statement('ALTER TABLE mentors MODIFY COLUMN last_name TEXT NOT NULL');
            DB::statement('ALTER TABLE mentors MODIFY COLUMN email TEXT NOT NULL');
            DB::statement('ALTER TABLE mentors MODIFY COLUMN company TEXT NULL');
        }

        DB::table('mentors')->get()->each(function ($row) {
            DB::table('mentors')->where('id', $row->id)->update([
                'first_name'   => Crypt::encrypt($row->first_name),
                'last_name'    => Crypt::encrypt($row->last_name),
                'email'        => Crypt::encrypt($row->email),
                'company'      => $row->company !== null ? Crypt::encrypt($row->company) : null,
                'email_hash'   => hash('sha256', strtolower(trim($row->email))),
                'search_index' => strtolower(trim($row->first_name . ' ' . $row->last_name . ' ' . $row->email)),
            ]);
        });

        Schema::table('mentors', function (Blueprint $table) {
            $table->string('email_hash', 64)->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        // Reverting encryption is destructive — encrypted data cannot be restored to plaintext without the original key.
        // This down() is intentionally left as a no-op to prevent accidental data loss.
    }
};
