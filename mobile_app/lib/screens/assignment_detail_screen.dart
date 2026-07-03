import 'dart:convert';

import 'package:flutter/material.dart';

import '../core/api_client.dart';
import '../models/assignment.dart';
import '../models/question.dart';
import '../widgets/state_views.dart';

class AssignmentDetailScreen extends StatefulWidget {
  const AssignmentDetailScreen({
    super.key,
    required this.apiClient,
    required this.assignment,
  });

  final ApiClient apiClient;
  final Assignment assignment;

  @override
  State<AssignmentDetailScreen> createState() => _AssignmentDetailScreenState();
}

class _AssignmentDetailScreenState extends State<AssignmentDetailScreen> {
  late Future<_AssignmentData> _future;
  final Map<int, dynamic> _answers = {};
  bool _submitting = false;

  @override
  void initState() {
    super.initState();
    _future = _load();
  }

  Future<_AssignmentData> _load() async {
    final response = await widget.apiClient.get('/assignments/${widget.assignment.id}');
    final assignment = Assignment.fromJson(
      response['assignment'] as Map<String, dynamic>,
    );
    final questions = (response['questions'] as List? ?? [])
        .whereType<Map<String, dynamic>>()
        .map(Question.fromJson)
        .toList();
    return _AssignmentData(assignment: assignment, questions: questions);
  }

  Future<void> _checkAnswer(Question question) async {
    final answer = _serializeAnswer(question, _answers[question.id]);
    final response = await widget.apiClient.post(
      '/assignments/${widget.assignment.id}/questions/${question.id}/check',
      body: {'answer': answer},
    );
    if (!mounted) return;
    final data = response['data'] as Map<String, dynamic>;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(data['is_correct'] == true ? 'Correct!' : 'Not correct yet.'),
      ),
    );
  }

  Future<void> _submit() async {
    setState(() => _submitting = true);
    try {
      final data = await _future;
      final answers = <String, String>{};
      for (final question in data.questions) {
        answers['${question.id}'] = _serializeAnswer(
          question,
          _answers[question.id],
        );
      }
      final response = await widget.apiClient.post(
        '/assignments/${widget.assignment.id}/submit',
        body: {'answers': answers},
      );
      if (!mounted) return;
      final submission = response['submission'] as Map<String, dynamic>;
      final result = submission['result'] as Map<String, dynamic>? ?? {};
      await showDialog<void>(
        context: context,
        builder: (_) => AlertDialog(
          title: const Text('Submitted'),
          content: Text(
            'Score: ${result['score'] ?? submission['score']}/${result['max_score'] ?? widget.assignment.maxScore}\n'
            'Correct: ${result['correct'] ?? 0}/${result['total'] ?? 0}',
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: const Text('OK'),
            ),
          ],
        ),
      );
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
    return Scaffold(
      appBar: AppBar(title: Text(widget.assignment.title)),
      body: FutureBuilder<_AssignmentData>(
        future: _future,
        builder: (context, snapshot) {
          if (snapshot.connectionState != ConnectionState.done) {
            return const LoadingView();
          }

          if (snapshot.hasError) {
            return ErrorStateView(
              message: snapshot.error.toString(),
              onRetry: () => setState(() => _future = _load()),
            );
          }

          final data = snapshot.data!;
          if (data.questions.isEmpty) {
            return const Center(child: Text('This homework has no questions yet.'));
          }

          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              Text(
                data.assignment.title,
                style: Theme.of(context).textTheme.titleLarge?.copyWith(
                      fontWeight: FontWeight.w900,
                    ),
              ),
              const SizedBox(height: 12),
              ...data.questions.asMap().entries.map(
                    (entry) => Padding(
                      padding: const EdgeInsets.only(bottom: 12),
                      child: _QuestionCard(
                        index: entry.key + 1,
                        question: entry.value,
                        answer: _answers[entry.value.id],
                        onChanged: (value) {
                          setState(() => _answers[entry.value.id] = value);
                        },
                        onCheck: () => _checkAnswer(entry.value),
                      ),
                    ),
                  ),
              const SizedBox(height: 8),
              FilledButton.icon(
                onPressed: _submitting ? null : _submit,
                icon: const Icon(Icons.send),
                label: Text(_submitting ? 'Submitting...' : 'Submit homework'),
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
    required this.question,
    required this.answer,
    required this.onChanged,
    required this.onCheck,
  });

  final int index;
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
            Text(
              'Question $index - ${question.typeLabel}',
              style: const TextStyle(fontWeight: FontWeight.w900),
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
              const SizedBox(height: 8),
              Text('Audio: ${question.audioUrl ?? question.group!.audioUrl}'),
            ],
            const SizedBox(height: 10),
            Text(question.text),
            const SizedBox(height: 12),
            _AnswerInput(
              question: question,
              answer: answer,
              onChanged: onChanged,
            ),
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
          children: question.options
              .map(
                (option) => RadioListTile<int>(
                  value: option.id,
                  groupValue: int.tryParse(answer?.toString() ?? ''),
                  onChanged: (value) => onChanged(value?.toString() ?? ''),
                  title: Text(option.text),
                ),
              )
              .toList(),
        );
      case 'ordering':
        final items = ((question.interactionData?['items'] as List?) ?? [])
            .map((item) => item.toString())
            .toList();
        final indexes = answer is List<int>
            ? answer as List<int>
            : List<int>.generate(items.length, (index) => index);
        return Column(
          children: [
            for (var i = 0; i < indexes.length; i++)
              ListTile(
                title: Text(items[indexes[i]]),
                leading: CircleAvatar(child: Text('${i + 1}')),
                trailing: Wrap(
                  children: [
                    IconButton(
                      onPressed: i == 0
                          ? null
                          : () {
                              final next = [...indexes];
                              final temp = next[i - 1];
                              next[i - 1] = next[i];
                              next[i] = temp;
                              onChanged(next);
                            },
                      icon: const Icon(Icons.arrow_upward),
                    ),
                    IconButton(
                      onPressed: i == indexes.length - 1
                          ? null
                          : () {
                              final next = [...indexes];
                              final temp = next[i + 1];
                              next[i + 1] = next[i];
                              next[i] = temp;
                              onChanged(next);
                            },
                      icon: const Icon(Icons.arrow_downward),
                    ),
                  ],
                ),
              ),
          ],
        );
      case 'matching':
        final pairs = ((question.interactionData?['pairs'] as List?) ?? [])
            .whereType<Map<String, dynamic>>()
            .toList();
        final selected = answer is Map ? Map<String, String>.from(answer) : {};
        return Column(
          children: [
            for (var leftIndex = 0; leftIndex < pairs.length; leftIndex++)
              Padding(
                padding: const EdgeInsets.only(bottom: 8),
                child: DropdownButtonFormField<String>(
                  value: selected['$leftIndex'],
                  decoration: InputDecoration(
                    labelText: pairs[leftIndex]['left']?.toString() ?? '',
                    border: const OutlineInputBorder(),
                  ),
                  items: [
                    for (var rightIndex = 0; rightIndex < pairs.length; rightIndex++)
                      DropdownMenuItem(
                        value: '$rightIndex',
                        child: Text(
                          pairs[rightIndex]['right_type'] == 'image'
                              ? 'Image ${rightIndex + 1}'
                              : pairs[rightIndex]['right']?.toString() ?? '',
                        ),
                      ),
                  ],
                  onChanged: (value) {
                    final next = Map<String, String>.from(selected);
                    if (value != null) next['$leftIndex'] = value;
                    onChanged(next);
                  },
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

class _AssignmentData {
  const _AssignmentData({required this.assignment, required this.questions});

  final Assignment assignment;
  final List<Question> questions;
}
