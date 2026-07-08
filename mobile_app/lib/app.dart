import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'core/router/app_router.dart';
import 'core/storage/secure_storage.dart';
import 'core/theme/app_theme.dart';

class App extends ConsumerWidget {
  const App({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return FutureBuilder<String?>(
      future: SecureStorage().readToken(),
      builder: (context, snapshot) {
        final hasToken = (snapshot.data ?? '').isNotEmpty;
        final router = AppRouter.create(
          initialLocation: hasToken ? '/home' : '/login',
        );

        return MaterialApp.router(
          title: 'LTEdu',
          debugShowCheckedModeBanner: false,
          theme: AppTheme.light,
          routerConfig: router,
        );
      },
    );
  }
}
