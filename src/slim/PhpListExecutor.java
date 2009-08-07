package slim;

import fitnesse.slim.ListExecutor;

public class PhpListExecutor extends ListExecutor {
  public PhpListExecutor() throws Exception {
    this(false);
  }
  
  public PhpListExecutor(boolean verbose) throws Exception {
    super(verbose, (new PhpSlimFactory()).getStatementExecutor());
  }
}
