import 'package:flutter/material.dart';

import '../core/api_client.dart';
import '../core/token_store.dart';
import '../models/school_class.dart';
import '../widgets/state_views.dart';
import 'class_detail_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({
    super.key,
    required this.apiClient,
    required this.onSignedOut,
  });

  final ApiClient apiClient;
  final VoidCallback onSignedOut;

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final _tokenStore = TokenStore();
  late Future<_HomeData> _future;

  @override
  void initState() {
    super.initState();
    _future = _load();
  }

  Future<_HomeData> _load() async {
    final name = await _tokenStore.readStudentName() ?? '';
    final response = await widget.apiClient.get('/classes');
    final classes = (response['data'] as List? ?? [])
        .whereType<Map<String, dynamic>>()
        .map(SchoolClass.fromJson)
        .toList();
    return _HomeData(studentName: name, classes: classes);
  }

  void _refresh() {
    setState(() => _future = _load());
  }

  Future<void> _logout() async {
    await widget.apiClient.logout();
    widget.onSignedOut();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('LTEdu'),
        actions: [
          IconButton(onPressed: _refresh, icon: const Icon(Icons.refresh)),
          IconButton(onPressed: _logout, icon: const Icon(Icons.logout)),
        ],
      ),
      body: FutureBuilder<_HomeData>(
        future: _future,
        builder: (context, snapshot) {
          if (snapshot.connectionState != ConnectionState.done) {
            return const LoadingView();
          }

          if (snapshot.hasError) {
            return ErrorStateView(
              message: snapshot.error.toString(),
              onRetry: _refresh,
            );
          }

          final data = snapshot.data!;
          return RefreshIndicator(
            onRefresh: () async => _refresh(),
            child: ListView(
              padding: const EdgeInsets.all(16),
              children: [
                Text(
                  data.studentName.isEmpty ? 'Hello!' : 'Hello, ${data.studentName}!',
                  style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                        fontWeight: FontWeight.w900,
                      ),
                ),
                const SizedBox(height: 6),
                const Text('Choose a class to see lessons and homework.'),
                const SizedBox(height: 18),
                if (data.classes.isEmpty)
                  const _EmptyClasses()
                else
                  ...data.classes.map(
                    (schoolClass) => Padding(
                      padding: const EdgeInsets.only(bottom: 12),
                      child: _ClassCard(
                        schoolClass: schoolClass,
                        onTap: () {
                          Navigator.of(context).push(
                            MaterialPageRoute(
                              builder: (_) => ClassDetailScreen(
                                apiClient: widget.apiClient,
                                schoolClass: schoolClass,
                              ),
                            ),
                          );
                        },
                      ),
                    ),
                  ),
              ],
            ),
          );
        },
      ),
    );
  }
}

class _ClassCard extends StatelessWidget {
  const _ClassCard({required this.schoolClass, required this.onTap});

  final SchoolClass schoolClass;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Card(
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              const CircleAvatar(child: Icon(Icons.menu_book)),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      schoolClass.name,
                      style: const TextStyle(fontWeight: FontWeight.w900),
                    ),
                    const SizedBox(height: 4),
                    Text('${schoolClass.classCode} - ${schoolClass.teacher ?? '-'}'),
                  ],
                ),
              ),
              const Icon(Icons.chevron_right),
            ],
          ),
        ),
      ),
    );
  }
}

class _EmptyClasses extends StatelessWidget {
  const _EmptyClasses();

  @override
  Widget build(BuildContext context) {
    return const Card(
      child: Padding(
        padding: EdgeInsets.all(18),
        child: Text('No classes yet. Your classes will appear here.'),
      ),
    );
  }
}

class _HomeData {
  const _HomeData({required this.studentName, required this.classes});

  final String studentName;
  final List<SchoolClass> classes;
}
