import 'package:flutter/material.dart';

import 'core/api_client.dart';
import 'core/token_store.dart';
import 'screens/home_screen.dart';
import 'screens/login_screen.dart';

void main() {
  runApp(const LTEduApp());
}

class LTEduApp extends StatelessWidget {
  const LTEduApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'LTEdu',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: const Color(0xFF2563EB)),
        useMaterial3: true,
        scaffoldBackgroundColor: const Color(0xFFF6F8FB),
        cardTheme: const CardTheme(
          margin: EdgeInsets.zero,
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.all(Radius.circular(16)),
          ),
        ),
      ),
      home: const AppGate(),
    );
  }
}

class AppGate extends StatefulWidget {
  const AppGate({super.key});

  @override
  State<AppGate> createState() => _AppGateState();
}

class _AppGateState extends State<AppGate> {
  final _tokenStore = TokenStore();
  final _apiClient = ApiClient();
  bool _loading = true;
  bool _signedIn = false;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final token = await _tokenStore.readToken();
    if (!mounted) return;
    setState(() {
      _signedIn = token != null && token.isNotEmpty;
      _loading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }

    if (!_signedIn) {
      return LoginScreen(
        apiClient: _apiClient,
        onSignedIn: () => setState(() => _signedIn = true),
      );
    }

    return HomeScreen(
      apiClient: _apiClient,
      onSignedOut: () => setState(() => _signedIn = false),
    );
  }
}
