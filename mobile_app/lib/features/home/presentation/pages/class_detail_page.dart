import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import 'package:ltedu_student/core/router/route_names.dart';
import 'package:ltedu_student/core/widgets/app_loading.dart';
import 'package:ltedu_student/features/home/domain/entities/assignment.dart';
import 'package:ltedu_student/features/home/domain/entities/class_entity.dart';
import 'package:ltedu_student/features/home/domain/entities/class_session.dart';
import 'package:ltedu_student/features/home/presentation/providers/class_sessions_provider.dart';

class ClassDetailPage extends ConsumerStatefulWidget {
  const ClassDetailPage({super.key, required this.schoolClass});

  final ClassEntity schoolClass;

  @override
  ConsumerState<ClassDetailPage> createState() => _ClassDetailPageState();
}

class _ClassDetailPageState extends ConsumerState<ClassDetailPage> {
  @override
  Widget build(BuildContext context) {
    final sessionsAsync =
        ref.watch(classSessionsProvider(widget.schoolClass.id));

    return Scaffold(
      appBar: AppBar(title: Text(widget.schoolClass.name)),
      body: sessionsAsync.when(
        loading: () => const AppLoading(),
        error: (error, stack) => AppErrorView(
          message: error.toString(),
          onRetry: () =>
              ref.invalidate(classSessionsProvider(widget.schoolClass.id)),
        ),
        data: (sessions) {
          return RefreshIndicator(
            onRefresh: () async =>
                ref.invalidate(classSessionsProvider(widget.schoolClass.id)),
            child: ListView(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
              children: [
                Text(
                  widget.schoolClass.name,
                  style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                        fontWeight: FontWeight.w900,
                      ),
                ),
                const SizedBox(height: 4),
                const Text('Lessons and homework'),
                const SizedBox(height: 14),
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
                      padding: const EdgeInsets.only(bottom: 10),
                      child: _SessionCard(
                        session: session,
                        onOpenAssignment: (assignment) {
                          context.pushNamed(
                            RouteNames.assignmentDetail,
                            pathParameters: {'id': '${assignment.id}'},
                            extra: assignment,
                          );
                        },
                      ),
                    ),
                  )
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
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: 42,
                  height: 42,
                  alignment: Alignment.center,
                  decoration: BoxDecoration(
                    color: statusColor.withValues(alpha: .10),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(
                    '${session.sessionNumber}',
                    style: TextStyle(
                      color: statusColor,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        session.title,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(fontWeight: FontWeight.w900),
                      ),
                      if (session.sessionDate != null) ...[
                        const SizedBox(height: 3),
                        Text(
                          session.sessionDate!,
                          style: const TextStyle(
                              color: Color(0xFF64748B), fontSize: 13),
                        ),
                      ],
                    ],
                  ),
                ),
                const SizedBox(width: 8),
                Chip(
                  label: Text(session.status),
                  visualDensity: VisualDensity.compact,
                  side: BorderSide(color: statusColor.withValues(alpha: .35)),
                  labelStyle: TextStyle(color: statusColor),
                ),
              ],
            ),
            if (session.isCancelled) ...[
              const SizedBox(height: 12),
              const _NoticeText(
                icon: Icons.info_outline,
                text:
                    'This lesson was cancelled. You do not need to do homework for it.',
              ),
            ] else if (session.assignments.isEmpty) ...[
              const SizedBox(height: 12),
              const _NoticeText(
                icon: Icons.assignment_outlined,
                text: 'No homework for this lesson yet.',
              ),
            ] else ...[
              const SizedBox(height: 12),
              ...session.assignments.map(
                (assignment) => Padding(
                  padding: const EdgeInsets.only(top: 8),
                  child: InkWell(
                    borderRadius: BorderRadius.circular(10),
                    onTap: () => onOpenAssignment(assignment),
                    child: Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: const Color(0xFFE2E8F0)),
                      ),
                      child: Row(
                        children: [
                          Container(
                            width: 36,
                            height: 36,
                            alignment: Alignment.center,
                            decoration: BoxDecoration(
                              color: assignment.latestSubmission == null
                                  ? const Color(0xFFEFF6FF)
                                  : const Color(0xFFDCFCE7),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: Icon(
                              assignment.latestSubmission == null
                                  ? Icons.assignment_outlined
                                  : Icons.check_circle_outline,
                              size: 20,
                              color: assignment.latestSubmission == null
                                  ? const Color(0xFF2563EB)
                                  : const Color(0xFF16A34A),
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  assignment.title,
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                  style: const TextStyle(
                                      fontWeight: FontWeight.w800),
                                ),
                                const SizedBox(height: 2),
                                Text(
                                  assignment.latestSubmission == null
                                      ? 'Tap to start'
                                      : 'Submitted - you can practice again',
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: const TextStyle(
                                    color: Color(0xFF64748B),
                                    fontSize: 13,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const Icon(Icons.chevron_right),
                        ],
                      ),
                    ),
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

class _NoticeText extends StatelessWidget {
  const _NoticeText({
    required this.icon,
    required this.text,
  });

  final IconData icon;
  final String text;

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 18, color: const Color(0xFF64748B)),
        const SizedBox(width: 8),
        Expanded(
          child: Text(
            text,
            style: const TextStyle(color: Color(0xFF64748B)),
          ),
        ),
      ],
    );
  }
}
