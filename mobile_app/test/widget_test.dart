import 'package:flutter_test/flutter_test.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'package:ltedu_student/app.dart';

void main() {
  testWidgets('app renders', (WidgetTester tester) async {
    await tester.pumpWidget(const ProviderScope(child: App()));
    await tester.pump();

    expect(find.text('LTEdu'), findsWidgets);
  });
}
