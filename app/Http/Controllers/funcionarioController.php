<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Funcionario;
use App\Models\Agendamento;

class FuncionarioController extends Controller
{
    /* Exibe uma lista de todos os funcionários. */
    public function index()
    {
        // Obtém todos os funcionários do banco de dados
        $funcionarios = Funcionario::all();

        // Retorna a view 'funcionarios.index' com a lista de funcionários
        return view('funcionarios.index', compact('funcionarios'));
    }

    /* Exibe o formulário para criar um novo funcionário. */
    public function create()
    {
        // Retorna a view 'funcionarios.create'
        return view('funcionarios.create');
    }

    /* Armazena um novo funcionário no banco de dados. */
    public function store(Request $request)
    {
        // Validação dos dados do formulário
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:funcionarios',
            'sexo' => 'required|string|in:M,F', // Supondo que o sexo seja M (Masculino) ou F (Feminino)
        ]);

        // Cria uma nova instância de Funcionario com dados do formulário
        $funcionario = new Funcionario([
            'nome' => $request->input('nome'),
            'email' => $request->input('email'),
            'sexo' => $request->input('sexo'),
        ]);

        // Salva o funcionário no banco de dados
        $funcionario->save();

        // Redireciona para a página 'funcionarios.index' após salvar
        return redirect()->route('funcionarios.index')->with('success', 'Funcionário criado com sucesso!');
    }

    /* Exibe o formulário para editar um funcionário existente. */
    public function edit(string $id)
    {
        // Encontra o funcionário pelo ID fornecido ou retorna 404 se não encontrado
        $funcionario = Funcionario::findOrFail($id);

        // Retorna a view 'funcionarios.edit' com o funcionário para edição
        return view('funcionarios.edit', compact('funcionario'));
    }

    /* Atualiza um funcionário existente no banco de dados. */
    public function update(Request $request, string $id)
    {
        // Encontra o funcionário pelo ID para atualização
        $funcionario = Funcionario::findOrFail($id);

        // Validação dos dados do formulário
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:funcionarios,email,'.$funcionario->id,
            'sexo' => 'required|string|in:M,F', // Supondo que o sexo seja M (Masculino) ou F (Feminino)
        ]);

        // Atualiza os campos do funcionário com os dados do formulário
        $funcionario->nome = $request->input('nome');
        $funcionario->email = $request->input('email');
        $funcionario->sexo = $request->input('sexo');

        // Salva as alterações no banco de dados
        $funcionario->save();

        // Redireciona para a página 'funcionarios.index' após a atualização
        return redirect()->route('funcionarios.index')->with('success', 'Funcionário alterado com sucesso!');
    }

    /* Remove um funcionário do banco de dados. */
    public function destroy(string $id)
    {
        // Encontra o funcionário pelo ID para exclusão
        $funcionario = Funcionario::findOrFail($id);

        // Verifica se o funcionário está vinculado a algum agendamento
        $agendamentos = Agendamento::where('funcionario_id', $funcionario->id)->exists();

        // Se estiver vinculado a algum agendamento, redireciona de volta com mensagem de erro
        if ($agendamentos) {
            return redirect()->route('funcionarios.index')->with('error', 'Não é possível excluir o funcionário, pois está vinculado a um agendamento.');
        }

        // Caso não esteja vinculado a agendamentos, exclui o funcionário
        $funcionario->delete();

        // Redireciona para a página 'funcionarios.index' após exclusão
        return redirect()->route('funcionarios.index')->with('success', 'Funcionário excluído com sucesso!');
    }

    /* Mostra detalhes de um funcionário específico. */
    public function show(string $id)
    {
        // Busca o funcionário pelo ID ou retorna 404 se não encontrado
        $funcionario = Funcionario::findOrFail($id);

        // Retorna a view 'funcionarios.show' para exibir detalhes do funcionário
        return view('funcionarios.show', compact('funcionario'));
    }
}
