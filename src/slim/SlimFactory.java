package slim;

import fitnesse.slim.StatementExecutorInterface;

public interface SlimFactory {

  public abstract StatementExecutorInterface getStatementExecutor()
      throws Exception;

}