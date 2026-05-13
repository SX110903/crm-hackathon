# LOPD / GDPR Compliance — HackCRM

## 1. Personal Data Stored

| Table         | Encrypted Fields                                      | Purpose                          |
|---------------|-------------------------------------------------------|----------------------------------|
| `users`       | `name`, `email`                                       | Admin user identity              |
| `participants`| `first_name`, `last_name`, `email`, `phone`, `university`, `major` | Hackathon participant identity |
| `judges`      | `first_name`, `last_name`, `email`, `company`         | Judge identity                   |
| `mentors`     | `first_name`, `last_name`, `email`, `company`         | Mentor identity                  |

## 2. Encryption at Rest (GDPR Article 32)

All personal data fields listed above are encrypted using **AES-256-CBC** via Laravel's built-in `encrypted` cast, which uses the application's `APP_KEY` as the encryption key.

- Algorithm: AES-256-CBC
- Key source: `APP_KEY` in `.env` (base64-encoded 32-byte key)
- Implementation: `Illuminate\Database\Eloquent\Casts\Attribute` (`encrypted` cast)
- The plaintext is never written to the database — only the encrypted ciphertext is stored
- Decryption happens automatically at the application layer when data is read

**Reference:** GDPR Article 32 — Security of processing; LOPD-GDD Article 32.

## 3. Email Lookup (SHA-256 Hash)

Email fields cannot be directly searched after encryption (each encryption produces a unique ciphertext). To allow efficient, secure email lookups without storing plaintext:

- A separate column `email_hash` (VARCHAR 64, UNIQUE) is maintained on each table
- `email_hash = SHA-256(lowercase(trim(email)))`
- SHA-256 is a one-way hash — it cannot be reversed to recover the original email
- All authentication and duplicate-check queries use `email_hash`, never the encrypted `email` column
- The `HasHashedEmail` trait (`app/Traits/HasHashedEmail.php`) automatically updates `email_hash` on every model save

## 4. Search Index

The `search_index` column (TEXT, nullable) on `participants`, `judges`, and `mentors` stores a lowercase concatenation of name and email for search purposes:

```
search_index = lowercase(first_name + ' ' + last_name + ' ' + email)
```

**Note:** `search_index` contains plaintext PII. This is an accepted trade-off under LOPD for authenticated admin users who already have legitimate access to this data. If stricter compliance is required, `search_index` can be removed and search disabled for encrypted tables.

## 5. Data Retention

- **Policy:** Personal data is retained for the duration of the hackathon event plus a 12-month period for audit and dispute resolution purposes.
- **Deletion:** See Section 6.

## 6. Right to Erasure (GDPR Article 17)

Data subjects may request deletion of their personal data. Administrators can delete records via:

- `DELETE /api/participants/{id}` — deletes a participant and their team memberships
- `DELETE /api/judges/{id}` — deletes a judge and their evaluations
- `DELETE /api/mentors/{id}` — deletes a mentor record

User (`admin`) accounts can only be deleted directly by a database administrator.

## 7. Data Access Controls

- All personal data endpoints require a valid Sanctum bearer token (`auth:sanctum` middleware)
- The public registration endpoint (`POST /api/register`) collects minimal PII (name, email, role) and immediately encrypts it
- No personal data is logged or included in error messages

## 8. Key Management

- The `APP_KEY` must be kept secret and backed up securely
- If `APP_KEY` is rotated, all encrypted data must be re-encrypted using the new key before the old key is discarded
- Key rotation procedure: `php artisan key:generate` followed by a re-encryption migration

## 9. Compliance References

- GDPR Article 32 — Technical measures (encryption at rest)
- GDPR Article 17 — Right to erasure
- GDPR Article 25 — Data protection by design and by default
- LOPD-GDD (Ley Orgánica 3/2018) — Adaptation of GDPR to Spanish law
- CISO control: all personal data fields classified as sensitive are encrypted at the storage layer
