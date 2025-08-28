# Tests Documentation

This document describes how to run the tests and shows the output of the final test run.

## Running Tests

To run all tests:

```bash
cd contacts-app
php artisan test
```

To run specific test files:

```bash
# Run only the required ContactsTest
php artisan test --filter="ContactsTest"

# Run authentication tests
php artisan test --filter="Auth"
```

## Required Tests

According to the project requirements, two specific tests were implemented:

### 1. Cross-Organization Isolation Test

**Test:** `cross-org isolation: org A cannot access org B contact`

**Purpose:** Ensures that users from Organization A cannot access contacts from Organization B.

**Implementation:** Creates two organizations with different users and contacts, then verifies that a user from Org B gets a 404 when trying to access a contact from Org A.

### 2. Duplicate Email Detection Test

**Test:** `duplicate email blocks creation and returns exact 422 payload`

**Purpose:** Verifies that attempting to create a contact with a duplicate email returns the exact 422 response format specified in the requirements.

**Expected Response:**

```json
{
  "code": "DUPLICATE_EMAIL",
  "existing_contact_id": "<uuid>"
}
```

**Implementation:** Creates a contact with an email, then attempts to create another contact with the same email (case-insensitive) and verifies the exact response format.

## Final Test Run Output

```
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true                                                                        0.06s

   PASS  Tests\Feature\Auth\AuthenticationTest
  ✓ login screen can be rendered                                                             1.44s
  ✓ users can authenticate using the login screen                                            0.18s
  ✓ users can not authenticate with invalid password                                         0.29s
  ✓ users can logout                                                                         0.14s

   PASS  Tests\Feature\Auth\EmailVerificationTest
  ✓ email verification screen can be rendered                                                0.10s
  ✓ email can be verified                                                                    0.08s
  ✓ email is not verified with invalid hash                                                  0.09s

   PASS  Tests\Feature\Auth\PasswordConfirmationTest
  ✓ confirm password screen can be rendered                                                  0.08s
  ✓ password can be confirmed                                                                0.08s
  ✓ password is not confirmed with invalid password                                          0.29s

   PASS  Tests\Feature\Auth\PasswordResetTest
  ✓ reset password link screen can be rendered                                               0.14s
  ✓ reset password link can be requested                                                     0.34s
  ✓ reset password screen can be rendered                                                    0.33s
  ✓ password can be reset with valid token                                                   0.40s

   PASS  Tests\Feature\Auth\PasswordUpdateTest
  ✓ password can be updated                                                                  0.11s
  ✓ correct password must be provided to update password                                     0.08s

   PASS  Tests\Feature\Auth\RegistrationTest
  ✓ registration screen can be rendered                                                      0.07s
  ✓ new users can register                                                                   0.09s

   PASS  Tests\Feature\ContactsTest
  ✓ cross-org isolation: org A cannot access org B contact                                   0.09s
  ✓ duplicate email blocks creation and returns exact 422 payload                            0.10s
  ✓ healthz endpoint returns ok                                                              0.05s

   PASS  Tests\Feature\ExampleTest
  ✓ it returns a successful response                                                         0.08s

   PASS  Tests\Feature\ProfileTest
  ✓ profile page is displayed                                                                0.07s
  ✓ profile information can be updated                                                       0.07s
  ✓ email verification status is unchanged when the email address is unchanged               0.06s
  ✓ user can delete their account                                                            0.10s
  ✓ correct password must be provided to delete account                                      0.08s

  Tests:    28 passed (66 assertions)
  Duration: 5.93s
```

## Test Coverage

The test suite includes:

- **28 tests** with **66 assertions**
- **Authentication tests** (login, logout, registration, password reset, email verification)
- **Profile management tests** (update profile, delete account, password changes)
- **Core application tests** (health check, cross-org isolation, duplicate detection)
- **Unit tests** for basic functionality

All tests pass successfully, ensuring the application meets the specified requirements for multi-organization contact management with proper isolation and duplicate detection.
