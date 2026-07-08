import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import 'package:ltedu_student/core/router/route_names.dart';
import 'package:ltedu_student/core/widgets/app_loading.dart';
import 'package:ltedu_student/features/auth/presentation/providers/auth_provider.dart';
import 'package:ltedu_student/features/home/presentation/providers/home_provider.dart';

class HomePage extends ConsumerWidget {
  const HomePage({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final classesAsync = ref.watch(classesProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('LTEdu'),
        actions: [
          IconButton(
            onPressed: () => ref.invalidate(classesProvider),
            icon: const Icon(Icons.refresh),
          ),
          IconButton(
            onPressed: () async {
              await ref.read(authStateProvider.notifier).logout();
              if (!context.mounted) return;
              context.go('/login');
            },
            icon: const Icon(Icons.logout),
          ),
        ],
      ),
      body: classesAsync.when(
        loading: () => const AppLoading(),
        error: (error, stack) => AppErrorView(
          message: error.toString(),
          onRetry: () => ref.invalidate(classesProvider),
        ),
        data: (classes) {
          return RefreshIndicator(
            onRefresh: () async => ref.invalidate(classesProvider),
            child: ListView(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
              children: [
                Text(
                  'Choose a class',
                  style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                        fontWeight: FontWeight.w900,
                      ),
                ),
                const SizedBox(height: 4),
                const Text('See your lessons and homework.'),
                const SizedBox(height: 16),
                if (classes.isEmpty)
                  const Card(
                    child: Padding(
                      padding: EdgeInsets.all(18),
                      child: Text(
                          'No classes yet. Your classes will appear here.'),
                    ),
                  )
                else
                  ...classes.map(
                    (schoolClass) => Padding(
                      padding: const EdgeInsets.only(bottom: 10),
                      child: Card(
                        child: InkWell(
                          borderRadius: BorderRadius.circular(14),
                          onTap: () => context.pushNamed(
                            RouteNames.classDetail,
                            pathParameters: {'id': schoolClass.id.toString()},
                            extra: schoolClass,
                          ),
                          child: Padding(
                            padding: const EdgeInsets.all(14),
                            child: Row(
                              children: [
                                Container(
                                  width: 44,
                                  height: 44,
                                  alignment: Alignment.center,
                                  decoration: BoxDecoration(
                                    color: const Color(0xFF2563EB)
                                        .withValues(alpha: .10),
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  child: const Icon(Icons.groups_2_outlined,
                                      color: Color(0xFF2563EB)),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        schoolClass.name,
                                        maxLines: 2,
                                        overflow: TextOverflow.ellipsis,
                                        style: const TextStyle(
                                            fontWeight: FontWeight.w900),
                                      ),
                                      const SizedBox(height: 3),
                                      Text(
                                        '${schoolClass.classCode} - ${schoolClass.teacher ?? '-'}',
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                        style: const TextStyle(
                                            color: Color(0xFF64748B)),
                                      ),
                                    ],
                                  ),
                                ),
                                const SizedBox(width: 8),
                                const Icon(Icons.chevron_right),
                              ],
                            ),
                          ),
                        ),
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
