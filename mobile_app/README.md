# LTEdu Student Mobile App

Flutter source for the LTEdu student app.

## Current Scope

- Student login by `student_code`
- Save API token locally
- View classes
- View lessons and homework
- Open homework
- Auth gate: if a token exists, the app opens Home directly
- Answer question types:
  - select
  - input
  - ordering
  - matching
- Check an answer
- Submit homework

## Architecture

- Flutter + Riverpod
- Dio for API calls
- GoRouter for navigation
- Secure storage for access token
- Feature-first structure under `lib/features`

## API Base URL

Default API URL is configured in `lib/core/app_config.dart`:

```dart
http://10.0.2.2/linh_trang/public/api/mobile
```

Use `10.0.2.2` for Android emulator. For a real phone, change it to your LAN/domain URL, for example:

```text
https://ltedu.pro/api/mobile
```

You can also override it at run time:

```bash
flutter run --dart-define=API_BASE_URL=https://ltedu.pro/api/mobile
```

## Run

Install Flutter first, then run:

```bash
cd mobile_app
flutter pub get
flutter run
```

This source currently contains the Dart app code. If Android/iOS platform folders are missing, initialize them with:

```bash
flutter create .
```

Then run `flutter pub get` again.
