import 'package:flutter/material.dart';

import '../core/api_client.dart';
import '../models/assignment.dart';
import '../models/class_session.dart';
import '../models/school_class.dart';
import '../widgets/state_views.dart';
import 'assignment_detail_screen.dart';

class ClassDetailScreen extends StatefulWidget {
  const ClassDetailScreen({
    super.key,
    required this.apiClient,
    required this.schoolClass,
  });

  final ApiClient apiClient;
  final SchoolClass schoolClass;

  @override
  State<ClassDetailScreen> createState() => _ClassDetailScreenState();
}

class _ClassDetailScreenState extends State<ClassDetailScreen> {
  late Future<List<ClassSession>> _future;

  @override
  void initState() {
    super.initState();
    _future = _load();
  }

  Future<List<ClassSession>> _load() async {
    final response = await widget.apiClient.get(
      '/classes/${widget.schoolClass.id}/sessions',
    );
    return (response['data'] as List? ?? [])
        .whereType<Map<String, dynamic>>()
        .map(ClassSession.fromJson)
        .toList();
  }

  void _refresh() {
    setState(() => _future = _load());
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(widget.schoolClass.name)),
      body: FutureBuilder<List<ClassSession>>(
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

          final sessions = snapshot.data!;
          return RefreshIndicator(
            onRefresh: () async => _refresh(),
            child: ListView(
              padding: const EdgeInsets.all(16),
              children: [
                Text(
                  'Lessons and homework',
                  style: Theme.of(context).textTheme.titleLarge?.copyWith(
                        fontWeight: FontWeight.w900,
                      ),
                ),
                const SizedBox(height: 12),
                if (sessions.isEmpty)
                  const Card(
                    child: Padding(
                      padding: EdgeInsets.all(18),
                      child: Text('No lessons yet.'),
                    ),
                  )
                else
                  ...sessions.map(
                    (session) => Padding(
                      padding: const EdgeInsets.only(bottom: 12),
                      child: _SessionCard(
                        session: session,
                        onOpenAssignment: (assignment) {
                          Navigator.of(context).push(
                            MaterialPageRoute(
                              builder: (_) => AssignmentDetailScreen(
                                apiClient: widget.apiClient,
                                assignment: assignment,
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

class _SessionCard extends StatelessWidget {
  const _SessionCard({
    required this.session,
    required this.onOpenAssignment,
  });

  final ClassSession session;
  final ValueChanged<Assignment> onOpenAssignment;

  @override
  Widget build(BuildContext context) {
    final statusColor = switch (session.status) {
      'completed' => Colors.green,
      'cancelled' => Colors.red,
      _ => Colors.blue,
    };

    return Card(
      color: session.isCancelled ? const Color(0xFFF1F5F9) : null,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Expanded(
                  child: Text(
                    'Lesson ${session.sessionNumber}: ${session.title}',
                    style: const TextStyle(fontWeight: FontWeight.w900),
                  ),
                ),
                Chip(
                  label: Text(session.status),
                  visualDensity: VisualDensity.compact,
                  side: BorderSide(color: statusColor.withOpacity(.35)),
                  labelStyle: TextStyle(color: statusColor),
                ),
              ],
            ),
            if (session.sessionDate != null) ...[
              const SizedBox(height: 4),
              Text(session.sessionDate!, style: const TextStyle(color: Colors.grey)),
            ],
            if (session.isCancelled) ...[
              const SizedBox(height: 12),
              const Text(
                'This lesson was cancelled. You do not need to do homework for it.',
              ),
            ] else if (session.assignments.isEmpty) ...[
              const SizedBox(height: 12),
              const Text('No homework for this lesson yet.'),
            ] else ...[
              const SizedBox(height: 12),
              ...session.assignments.map(
                (assignment) => Padding(
                  padding: const EdgeInsets.only(top: 8),
                  child: _AssignmentTile(
                    assignment: assignment,
                    onTap: () => onOpenAssignment(assignment),
                  ),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}

class _AssignmentTile extends StatelessWidget {
  const _AssignmentTile({required this.assignment, required this.onTap});

  final Assignment assignment;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final submitted = assignment.latestSubmission != null;
    return InkWell(
      borderRadius: BorderRadius.circular(12),
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: const Color(0xFFF8FAFC),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: const Color(0xFFE2E8F0)),
        ),
        child: Row(
          children: [
            Icon(submitted ? Icons.check_circle : Icons.assignment),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    assignment.title,
                    style: const TextStyle(fontWeight: FontWeight.w800),
                  ),
                  Text(submitted ? 'Submitted' : 'Not submitted'),
                ],
              ),
            ),
            const Icon(Icons.chevron_right),
          ],
        ),
      ),
    );
  }
}
