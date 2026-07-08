import 'dart:convert';

// TODO: Replace RadioListTile/FormField deprecated members when the new
// Flutter RadioGroup API is stable across our target SDKs.
// ignore_for_file: deprecated_member_use

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'package:ltedu_student/core/widgets/app_loading.dart';
import 'package:ltedu_student/features/home/domain/entities/assignment.dart';
import 'package:ltedu_student/features/home/domain/entities/question.dart';
import 'package:ltedu_student/features/home/presentation/providers/assignment_detail_provider.dart';

class AssignmentDetailPage extends ConsumerStatefulWidget {
  const AssignmentDetailPage({super.key, required this.assignment});

  final Assignment assignment;

  @override
  ConsumerState<AssignmentDetailPage> createState() =>
      _AssignmentDetailPageState();
}

class _AssignmentDetailPageState extends ConsumerState<AssignmentDetailPage> {
  final Map<int, dynamic> _answers = {};
  bool _submitting = false;

  Future<void> _checkAnswer(Question question) async {
    final answer = _serializeAnswer(question, _answers[question.id]);
    final result = await ref.read(checkAnswerUseCaseProvider).call(
          assignmentId: widget.assignment.id,
          questionId: question.id,
          answer: answer,
        );

    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
            result['is_correct'] == true ? 'Correct!' : 'Not correct yet.'),
      ),
    );
  }

  Future<void> _submit(List<Question> questions) async {
    setState(() => _submitting = true);
    try {
      final answers = <String, String>{};
      for (final question in questions) {
        answers['${question.id}'] =
            _serializeAnswer(question, _answers[question.id]);
      }

      final submission = await ref.read(submitAssignmentUseCaseProvider).call(
            assignmentId: widget.assignment.id,
            answers: answers,
          );

      if (!mounted) return;
      final result = submission['result'] as Map<String, dynamic>? ?? {};
      await showDialog<void>(
        context: context,
        builder: (_) => AlertDialog(
          title: const Text('Submitted'),
          content: Text(
            'Score: ${result['score'] ?? submission['score']}/${result['max_score'] ?? widget.assignment.maxScore}\n'
            'Correct: ${result['correct'] ?? 0}/${result['total'] ?? questions.length}',
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: const Text('OK'),
            ),
          ],
        ),
      );
      ref.invalidate(assignmentDetailProvider(widget.assignment.id));
    } catch (error) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(error.toString())),
      );
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }

  String _serializeAnswer(Question question, dynamic value) {
    if (value == null) return '';
    if (question.questionType == 'ordering' && value is List<int>) {
      return value.join(',');
    }
    if (question.questionType == 'matching' && value is Map) {
      return jsonEncode(value);
    }
    return value.toString();
  }

  @override
  Widget build(BuildContext context) {
    final detailAsync =
        ref.watch(assignmentDetailProvider(widget.assignment.id));

    return Scaffold(
      appBar: AppBar(title: Text(widget.assignment.title)),
      body: detailAsync.when(
        loading: () => const AppLoading(),
        error: (error, stack) => AppErrorView(
          message: error.toString(),
          onRetry: () =>
              ref.invalidate(assignmentDetailProvider(widget.assignment.id)),
        ),
        data: (detail) {
          final questions = detail.questions;
          final answeredCount = questions.where((question) {
            final answer = _answers[question.id];
            if (answer == null) return false;
            if (answer is String) return answer.trim().isNotEmpty;
            if (answer is Map) return answer.isNotEmpty;
            if (answer is List) return answer.isNotEmpty;
            return true;
          }).length;

          return Column(
            children: [
              Expanded(
                child: ListView(
                  padding: const EdgeInsets.fromLTRB(16, 8, 16, 16),
                  children: [
                    _AssignmentHeader(
                      title: detail.assignment.title,
                      maxScore: detail.assignment.maxScore,
                      answeredCount: answeredCount,
                      totalCount: questions.length,
                    ),
                    const SizedBox(height: 14),
                    if (questions.isEmpty)
                      const Card(
                        child: Padding(
                          padding: EdgeInsets.all(18),
                          child: Text('This homework has no questions yet.'),
                        ),
                      )
                    else
                      ...questions.asMap().entries.map(
                            (entry) => Padding(
                              padding: const EdgeInsets.only(bottom: 12),
                              child: _QuestionCard(
                                index: entry.key + 1,
                                total: questions.length,
                                question: entry.value,
                                answer: _answers[entry.value.id],
                                onChanged: (value) => setState(
                                    () => _answers[entry.value.id] = value),
                                onCheck: () => _checkAnswer(entry.value),
                              ),
                            ),
                          ),
                  ],
                ),
              ),
              if (questions.isNotEmpty)
                SafeArea(
                  top: false,
                  child: Container(
                    padding: const EdgeInsets.fromLTRB(16, 10, 16, 12),
                    decoration: const BoxDecoration(
                      color: Colors.white,
                      border: Border(top: BorderSide(color: Color(0xFFE2E8F0))),
                    ),
                    child: FilledButton.icon(
                      onPressed: _submitting ? null : () => _submit(questions),
                      icon: const Icon(Icons.send),
                      label: Text(
                          _submitting ? 'Submitting...' : 'Submit homework'),
                    ),
                  ),
                ),
            ],
          );
        },
      ),
    );
  }
}

class _QuestionCard extends StatelessWidget {
  const _QuestionCard({
    required this.index,
    required this.total,
    required this.question,
    required this.answer,
    required this.onChanged,
    required this.onCheck,
  });

  final int index;
  final int total;
  final Question question;
  final dynamic answer;
  final ValueChanged<dynamic> onChanged;
  final VoidCallback onCheck;

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                  decoration: BoxDecoration(
                    color: const Color(0xFF2563EB).withValues(alpha: .10),
                    borderRadius: BorderRadius.circular(999),
                  ),
                  child: Text(
                    '$index/$total',
                    style: const TextStyle(
                      color: Color(0xFF2563EB),
                      fontWeight: FontWeight.w900,
                      fontSize: 12,
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    question.typeLabel,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                      color: Color(0xFF64748B),
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ),
              ],
            ),
            if (question.group?.passage?.isNotEmpty == true) ...[
              const SizedBox(height: 10),
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: const Color(0xFFFFF7ED),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text(question.group!.passage!),
              ),
            ],
            if (question.group?.audioUrl?.isNotEmpty == true ||
                question.audioUrl?.isNotEmpty == true) ...[
              const SizedBox(height: 10),
              OutlinedButton.icon(
                onPressed: () {},
                icon: const Icon(Icons.volume_up_outlined),
                label: const Text('Audio file'),
              ),
            ],
            const SizedBox(height: 12),
            Text(
              question.text,
              style: const TextStyle(
                fontSize: 16,
                height: 1.35,
                fontWeight: FontWeight.w800,
              ),
            ),
            const SizedBox(height: 12),
            _AnswerInput(
                question: question, answer: answer, onChanged: onChanged),
            const SizedBox(height: 10),
            OutlinedButton.icon(
              onPressed: onCheck,
              icon: const Icon(Icons.fact_check),
              label: const Text('Check'),
            ),
          ],
        ),
      ),
    );
  }
}

class _AssignmentHeader extends StatelessWidget {
  const _AssignmentHeader({
    required this.title,
    required this.maxScore,
    required this.answeredCount,
    required this.totalCount,
  });

  final String title;
  final double maxScore;
  final int answeredCount;
  final int totalCount;

  @override
  Widget build(BuildContext context) {
    final progress = totalCount == 0 ? 0.0 : answeredCount / totalCount;

    return Card(
      color: const Color(0xFF0F172A),
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              title,
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                color: Colors.white,
                fontSize: 18,
                fontWeight: FontWeight.w900,
              ),
            ),
            const SizedBox(height: 10),
            Row(
              children: [
                Expanded(
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(999),
                    child: LinearProgressIndicator(
                      minHeight: 8,
                      value: progress,
                      backgroundColor: Colors.white24,
                      valueColor:
                          const AlwaysStoppedAnimation(Color(0xFF38BDF8)),
                    ),
                  ),
                ),
                const SizedBox(width: 10),
                Text(
                  '$answeredCount/$totalCount',
                  style: const TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.w800,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              'Max score: $maxScore',
              style: const TextStyle(color: Colors.white70),
            ),
          ],
        ),
      ),
    );
  }
}

class _AnswerInput extends StatelessWidget {
  const _AnswerInput({
    required this.question,
    required this.answer,
    required this.onChanged,
  });

  final Question question;
  final dynamic answer;
  final ValueChanged<dynamic> onChanged;

  @override
  Widget build(BuildContext context) {
    switch (question.questionType) {
      case 'select':
        return Column(
          children: question.options.map(
            (option) {
              final selected =
                  int.tryParse(answer?.toString() ?? '') == option.id;
              return Padding(
                padding: const EdgeInsets.only(bottom: 8),
                child: InkWell(
                  borderRadius: BorderRadius.circular(12),
                  onTap: () => onChanged(option.id.toString()),
                  child: Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: selected ? const Color(0xFFEFF6FF) : Colors.white,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(
                        color: selected
                            ? const Color(0xFF2563EB)
                            : const Color(0xFFE2E8F0),
                        width: selected ? 1.4 : 1,
                      ),
                    ),
                    child: Row(
                      children: [
                        Icon(
                          selected
                              ? Icons.radio_button_checked
                              : Icons.radio_button_off,
                          color: selected
                              ? const Color(0xFF2563EB)
                              : const Color(0xFF94A3B8),
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: Text(
                            option.text,
                            style: TextStyle(
                              fontWeight:
                                  selected ? FontWeight.w800 : FontWeight.w500,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
          ).toList(),
        );
      case 'ordering':
        final items = ((question.interactionData?['items'] as List?) ?? [])
            .map((item) => item.toString())
            .toList();
        final indexes = answer is List<int>
            ? answer as List<int>
            : List<int>.generate(items.length, (index) => index);
        return ReorderableListView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          buildDefaultDragHandles: false,
          itemCount: indexes.length,
          onReorder: (oldIndex, newIndex) {
            final next = [...indexes];
            if (newIndex > oldIndex) newIndex -= 1;
            final item = next.removeAt(oldIndex);
            next.insert(newIndex, item);
            onChanged(next);
          },
          itemBuilder: (context, i) {
            return Padding(
              key: ValueKey(indexes[i]),
              padding: const EdgeInsets.only(bottom: 8),
              child: Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Row(
                  children: [
                    CircleAvatar(
                      radius: 15,
                      backgroundColor: const Color(0xFF2563EB),
                      child: Text(
                        '${i + 1}',
                        style:
                            const TextStyle(color: Colors.white, fontSize: 12),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(child: Text(items[indexes[i]])),
                    ReorderableDragStartListener(
                      index: i,
                      child: const Icon(Icons.drag_indicator,
                          color: Color(0xFF64748B)),
                    ),
                  ],
                ),
              ),
            );
          },
        );
      case 'matching':
        final pairs = ((question.interactionData?['pairs'] as List?) ?? [])
            .whereType<Map<String, dynamic>>()
            .toList();
        final selected = answer is Map
            ? Map<String, String>.from(answer)
            : <String, String>{};
        return Column(
          children: [
            for (var leftIndex = 0; leftIndex < pairs.length; leftIndex++)
              Padding(
                padding: const EdgeInsets.only(bottom: 8),
                child: Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: const Color(0xFFE2E8F0)),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        pairs[leftIndex]['left']?.toString() ?? '',
                        style: const TextStyle(fontWeight: FontWeight.w800),
                      ),
                      const SizedBox(height: 8),
                      DropdownButtonFormField<String>(
                        initialValue: selected['$leftIndex'],
                        isExpanded: true,
                        decoration: const InputDecoration(
                          hintText: 'Choose the matching answer',
                          border: OutlineInputBorder(),
                        ),
                        items: [
                          for (var rightIndex = 0;
                              rightIndex < pairs.length;
                              rightIndex++)
                            DropdownMenuItem(
                              value: '$rightIndex',
                              child: Text(
                                pairs[rightIndex]['right_type'] == 'image'
                                    ? 'Image ${rightIndex + 1}'
                                    : pairs[rightIndex]['right']?.toString() ??
                                        '',
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                        ],
                        onChanged: (value) {
                          final next = Map<String, String>.from(selected);
                          if (value != null) next['$leftIndex'] = value;
                          onChanged(next);
                        },
                      ),
                    ],
                  ),
                ),
              ),
          ],
        );
      default:
        return TextFormField(
          initialValue: answer?.toString(),
          minLines: 1,
          maxLines: 4,
          decoration: const InputDecoration(
            border: OutlineInputBorder(),
            hintText: 'Type your answer',
          ),
          onChanged: onChanged,
        );
    }
  }
}
