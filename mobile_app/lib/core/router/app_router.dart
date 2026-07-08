import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../../features/auth/presentation/pages/login_page.dart';
import '../../features/home/domain/entities/assignment.dart';
import '../../features/home/domain/entities/class_entity.dart';
import '../../features/home/presentation/pages/assignment_detail_page.dart';
import '../../features/home/presentation/pages/class_detail_page.dart';
import '../../features/home/presentation/pages/home_page.dart';
import 'route_names.dart';

class AppRouter {
  AppRouter._();

  static GoRouter create({String initialLocation = '/login'}) {
    return GoRouter(
      initialLocation: initialLocation,
      routes: [
        GoRoute(
          path: '/login',
          name: RouteNames.login,
          builder: (context, state) => const LoginPage(),
        ),
        GoRoute(
          path: '/home',
          name: RouteNames.home,
          builder: (context, state) => const HomePage(),
        ),
        GoRoute(
          path: '/classes/:id',
          name: RouteNames.classDetail,
          builder: (context, state) {
            final schoolClass = state.extra as ClassEntity?;
            if (schoolClass == null) {
              return const Scaffold(
                body: Center(child: Text('Class details are unavailable.')),
              );
            }
            return ClassDetailPage(schoolClass: schoolClass);
          },
        ),
        GoRoute(
          path: '/assignments/:id',
          name: RouteNames.assignmentDetail,
          builder: (context, state) {
            final assignment = state.extra as Assignment?;
            if (assignment == null) {
              return const Scaffold(
                body: Center(child: Text('Homework details are unavailable.')),
              );
            }
            return AssignmentDetailPage(assignment: assignment);
          },
        ),
      ],
    );
  }
}
